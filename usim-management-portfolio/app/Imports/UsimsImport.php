<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Usim;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsimsImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;
    public int $updated = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $usimNumber = trim((string) ($row['유심번호'] ?? ''));

            if ($usimNumber === '') {
                continue;
            }

            $rowNumber = $index + 2;

            $status = trim((string) ($row['상태'] ?? '')) ?: Usim::STATUS_CONTRACT;
            if (! in_array($status, Usim::STATUSES, true)) {
                $this->errors[] = "{$rowNumber}행: 알 수 없는 상태값 '{$status}' (건너뜀)";
                continue;
            }

            $customerId = null;
            $customerName = trim((string) ($row['고객명'] ?? ''));
            if ($customerName !== '') {
                $customerId = Customer::firstOrCreate(['name' => $customerName])->id;
            }

            $deviceId = null;
            $serial = trim((string) ($row['기기일련번호'] ?? ''));
            if ($serial !== '') {
                $deviceId = Device::firstOrCreate(
                    ['serial_number' => $serial],
                    ['model_name' => trim((string) ($row['기기모델'] ?? '')) ?: $serial]
                )->id;
            }

            $data = [
                'phone_number' => $this->nullableString($row['일련번호'] ?? null),
                'carrier' => $this->nullableString($row['통신사'] ?? null),
                'site' => $this->nullableString($row['거래처/현장'] ?? null),
                'customer_id' => $customerId,
                'device_id' => $deviceId,
                'status' => $status,
                'contract_date' => $this->parseDate($row['계약일'] ?? null),
                'suspended_date' => $this->parseDate($row['일시정지일'] ?? null),
                'canceled_date' => $this->parseDate($row['해지일'] ?? null),
                'memo' => $this->nullableString($row['메모'] ?? null),
            ];

            $usim = Usim::where('usim_number', $usimNumber)->first();

            if ($usim) {
                $usim->update($data);
                $this->updated++;
            } else {
                $data['usim_number'] = $usimNumber;
                Usim::create($data);
                $this->created++;
            }
        }
    }

    private function nullableString($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return \Carbon\Carbon::parse((string) $value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
