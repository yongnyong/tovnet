<?php

namespace App\Exports;

use App\Models\Usim;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsimsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $query = Usim::query()->with(['customer', 'device']);

        if (! empty($this->filters['keyword'])) {
            $keyword = $this->filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('usim_number', 'like', "%{$keyword}%")
                    ->orWhere('phone_number', 'like', "%{$keyword}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('device', fn ($dq) => $dq->where('serial_number', 'like', "%{$keyword}%"));
            });
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['site'])) {
            $query->where('site', $this->filters['site']);
        }

        return $query->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return ['유심번호', '일련번호', '통신사', '거래처/현장', '고객명', '기기모델', '기기일련번호', '상태', '계약일', '일시정지일', '해지일', '메모'];
    }

    public function map($usim): array
    {
        return [
            $usim->usim_number,
            $usim->phone_number,
            $usim->carrier,
            $usim->site,
            $usim->customer->name ?? '',
            $usim->device->model_name ?? '',
            $usim->device->serial_number ?? '',
            $usim->status,
            optional($usim->contract_date)->toDateString(),
            optional($usim->suspended_date)->toDateString(),
            optional($usim->canceled_date)->toDateString(),
            $usim->memo,
        ];
    }
}
