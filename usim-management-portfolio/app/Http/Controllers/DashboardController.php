<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Usim;
use App\Models\UsimStatusHistory;

class DashboardController extends Controller
{
    public function index()
    {
        $statusCounts = Usim::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total_usims' => Usim::count(),
            'total_customers' => Customer::count(),
            'total_devices' => Device::count(),
            'contract' => $statusCounts[Usim::STATUS_CONTRACT] ?? 0,
            'suspended' => $statusCounts[Usim::STATUS_SUSPENDED] ?? 0,
            'canceled' => $statusCounts[Usim::STATUS_CANCELED] ?? 0,
        ];

        $recentHistories = UsimStatusHistory::with(['usim', 'changedByUser'])
            ->latest('changed_date')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'recentHistories'));
    }
}
