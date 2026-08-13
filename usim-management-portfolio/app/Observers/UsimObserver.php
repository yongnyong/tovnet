<?php

namespace App\Observers;

use App\Models\Usim;
use App\Models\UsimStatusHistory;
use Illuminate\Support\Facades\Auth;

class UsimObserver
{
    /**
     * Handle the Usim "created" event.
     *
     * @param  \App\Models\Usim  $usim
     * @return void
     */
    public function created(Usim $usim)
    {
        UsimStatusHistory::create([
            'usim_id' => $usim->id,
            'status' => $usim->status,
            'changed_date' => $this->dateForStatus($usim) ?? now()->toDateString(),
            'changed_by' => Auth::id(),
            'memo' => '유심 등록',
        ]);
    }

    /**
     * Handle the Usim "updated" event.
     *
     * @param  \App\Models\Usim  $usim
     * @return void
     */
    public function updated(Usim $usim)
    {
        if (! $usim->wasChanged('status')) {
            return;
        }

        UsimStatusHistory::create([
            'usim_id' => $usim->id,
            'status' => $usim->status,
            'changed_date' => $this->dateForStatus($usim) ?? now()->toDateString(),
            'changed_by' => Auth::id(),
            'memo' => '상태 변경: ' . $usim->getOriginal('status') . ' → ' . $usim->status,
        ]);
    }

    private function dateForStatus(Usim $usim): ?string
    {
        return match ($usim->status) {
            Usim::STATUS_CONTRACT => optional($usim->contract_date)->toDateString(),
            Usim::STATUS_SUSPENDED => optional($usim->suspended_date)->toDateString(),
            Usim::STATUS_CANCELED => optional($usim->canceled_date)->toDateString(),
            default => null,
        };
    }
}
