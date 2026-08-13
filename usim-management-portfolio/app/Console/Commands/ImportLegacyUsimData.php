<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Device;
use App\Models\User;
use App\Models\Usim;
use App\Models\UsimStatusHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportLegacyUsimData extends Command
{
    protected $signature = 'usim:import-legacy {file} {--dry-run}';

    protected $description = '레거시 엑셀 자료(특정 시트 구조 대응)를 유심관리시스템으로 이관하는 1회성 스크립트';

    private const STATUS_MAP = [
        '사용중' => Usim::STATUS_CONTRACT,
        '일시정지' => Usim::STATUS_SUSPENDED,
        '해지완료' => Usim::STATUS_CANCELED,
    ];

    public function handle()
    {
        $path = $this->argument('file');
        $dryRun = $this->option('dry-run');

        if (! file_exists($path)) {
            $this->error("파일을 찾을 수 없습니다: {$path}");

            return Command::FAILURE;
        }

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Auth::login($admin);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('④전체통합건');

        if (! $sheet) {
            $this->error('④전체통합건 시트를 찾을 수 없습니다.');

            return Command::FAILURE;
        }

        $lastRow = $sheet->getHighestDataRow();

        $created = 0;
        $skippedNoUsimNumber = [];
        $skippedDuplicate = [];
        $seenUsimNumbers = [];

        DB::beginTransaction();

        try {
            for ($row = 4; $row <= $lastRow; $row++) {
                $get = fn ($col) => $this->cellValue($sheet, "{$col}{$row}");

                $usimNumber = $this->normalizeNumberLike($get('F'));
                $ctn = trim((string) $get('H'));
                $customerName = trim((string) $get('I'));

                if ($usimNumber === '') {
                    $skippedNoUsimNumber[] = [
                        'row' => $row,
                        'ctn' => $ctn,
                        'customer' => $customerName,
                    ];
                    continue;
                }

                if (isset($seenUsimNumbers[$usimNumber])) {
                    $skippedDuplicate[] = ['row' => $row, 'usim_number' => $usimNumber];
                    continue;
                }
                $seenUsimNumbers[$usimNumber] = true;

                $division = trim((string) $get('A'));
                $deviceModel = trim((string) $get('C'));
                $deviceSerial = $this->normalizeNumberLike($get('D'));
                $usimType = trim((string) $get('E'));
                $billingAccount = $this->normalizeNumberLike($get('G'));
                $activatedAt = trim((string) $get('J'));
                $changedActivatedAt = trim((string) $get('K'));
                $plan = trim((string) $get('L'));
                $suspendedAt = trim((string) $get('M'));
                $statusRaw = trim((string) $get('N'));
                $note = trim((string) $get('O'));
                $site = trim((string) $get('P'));
                $cancelFlag = trim((string) $get('Q'));
                $soldAt = trim((string) $get('R'));
                $extraMemo = trim((string) $get('S'));

                $status = self::STATUS_MAP[$statusRaw] ?? Usim::STATUS_CONTRACT;

                $contractDate = $this->parseDate($changedActivatedAt) ?? $this->parseDate($activatedAt);
                $suspendedDate = $this->parseDate($suspendedAt);

                $canceledDate = null;
                $cancelDateUncertain = false;
                if ($status === Usim::STATUS_CANCELED) {
                    $canceledDate = $suspendedDate ?? $contractDate;
                    $cancelDateUncertain = true;
                }

                $customer = null;
                if ($customerName !== '') {
                    $customer = Customer::firstOrCreate(['name' => $customerName]);
                }

                $device = null;
                if ($deviceSerial !== '') {
                    $device = Device::firstOrCreate(
                        ['serial_number' => $deviceSerial],
                        ['model_name' => $deviceModel !== '' ? $deviceModel : $deviceSerial]
                    );
                }

                $memoLines = [];
                if ($division !== '') $memoLines[] = "구분: {$division}";
                if ($usimType !== '') $memoLines[] = "유심종류: {$usimType}";
                if ($billingAccount !== '') $memoLines[] = "청구계정번호: {$billingAccount}";
                if ($plan !== '') $memoLines[] = "요금제: {$plan}";
                if ($activatedAt !== '' && $changedActivatedAt !== '') $memoLines[] = "원본 개통일자: {$activatedAt}";
                if ($note !== '') $memoLines[] = "비고: {$note}";
                if ($cancelFlag !== '') $memoLines[] = "해약 표시: {$cancelFlag}";
                if ($soldAt !== '') $memoLines[] = "판매날짜: {$soldAt}";
                if ($extraMemo !== '') $memoLines[] = "추가메모: {$extraMemo}";
                if ($cancelDateUncertain) $memoLines[] = "※ 정확한 해지일자 확인 필요 (원본 자료에 해지일 컬럼 없음)";
                $memoLines[] = '[기존 엑셀자료 이관 - 원본 행 ' . $row . ']';

                // Bypass UsimObserver here: it only knows how to write a single
                // "created" history row for the current status. We need the full
                // chronological chain (계약 -> 일시정지 -> 해지), inserted in real
                // order so that the id-tiebreak in statusHistories() sorts same-date
                // entries correctly.
                $usim = Usim::withoutEvents(function () use (
                    $usimNumber, $ctn, $site, $customer, $device, $status,
                    $contractDate, $suspendedDate, $canceledDate, $memoLines
                ) {
                    return Usim::create([
                        'usim_number' => $usimNumber,
                        'phone_number' => $ctn !== '' ? $ctn : null,
                        'carrier' => null,
                        'site' => $site !== '' ? $site : null,
                        'customer_id' => $customer?->id,
                        'device_id' => $device?->id,
                        'status' => $status,
                        'contract_date' => $contractDate,
                        'suspended_date' => $suspendedDate,
                        'canceled_date' => $canceledDate,
                        'memo' => implode("\n", $memoLines),
                    ]);
                });

                UsimStatusHistory::create([
                    'usim_id' => $usim->id,
                    'status' => Usim::STATUS_CONTRACT,
                    'changed_date' => $contractDate ?? now()->toDateString(),
                    'changed_by' => $admin?->id,
                    'memo' => '유심 등록 (기존 자료 이관)',
                ]);

                if (in_array($status, [Usim::STATUS_SUSPENDED, Usim::STATUS_CANCELED], true) && $suspendedDate) {
                    UsimStatusHistory::create([
                        'usim_id' => $usim->id,
                        'status' => Usim::STATUS_SUSPENDED,
                        'changed_date' => $suspendedDate,
                        'changed_by' => $admin?->id,
                        'memo' => '일시정지 처리 (기존 자료 이관)',
                    ]);
                }

                if ($status === Usim::STATUS_CANCELED) {
                    UsimStatusHistory::create([
                        'usim_id' => $usim->id,
                        'status' => Usim::STATUS_CANCELED,
                        'changed_date' => $canceledDate ?? now()->toDateString(),
                        'changed_by' => $admin?->id,
                        'memo' => '해지 처리 (기존 자료 이관, 정확한 해지일자 확인 필요)',
                    ]);
                }

                $created++;
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('이관 중 오류: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "등록 {$created}건, 유심번호 없음(건너뜀) " . count($skippedNoUsimNumber) . '건, 중복(건너뜀) ' . count($skippedDuplicate) . '건');

        if ($skippedNoUsimNumber) {
            $this->warn('유심번호 없어서 건너뛴 행:');
            foreach ($skippedNoUsimNumber as $s) {
                $this->line("  row {$s['row']}: CTN={$s['ctn']} 고객명={$s['customer']}");
            }
        }

        if ($skippedDuplicate) {
            $this->warn('중복이라 건너뛴 행:');
            foreach ($skippedDuplicate as $s) {
                $this->line("  row {$s['row']}: 유심번호={$s['usim_number']}");
            }
        }

        return Command::SUCCESS;
    }

    private function cellValue($sheet, string $coordinate)
    {
        return $sheet->getCell($coordinate)->getValue();
    }

    private function normalizeNumberLike($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value) || is_int($value)) {
            if (is_float($value) && floor($value) == $value) {
                return (string) (int) $value;
            }

            return (string) $value;
        }

        return trim((string) $value);
    }

    private function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $normalized = str_replace('.', '-', $value);
        $normalized = rtrim($normalized, '-');

        try {
            return Carbon::parse($normalized)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
