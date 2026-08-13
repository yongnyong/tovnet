<?php

namespace App\Http\Controllers;

use App\Exports\UsimsExport;
use App\Imports\UsimsImport;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Usim;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UsimController extends Controller
{
    public function index(Request $request)
    {
        $query = Usim::query()->with(['customer', 'device']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('usim_number', 'like', "%{$keyword}%")
                    ->orWhere('phone_number', 'like', "%{$keyword}%")
                    ->orWhereHas('customer', function ($cq) use ($keyword) {
                        $cq->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('device', function ($dq) use ($keyword) {
                        $dq->where('serial_number', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('site')) {
            $query->where('site', $request->site);
        }

        $usims = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $sites = Usim::whereNotNull('site')->distinct()->orderBy('site')->pluck('site');

        return view('usims.index', compact('usims', 'sites'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $devices = Device::whereDoesntHave('usim')->orderBy('model_name')->get();

        return view('usims.create', compact('customers', 'devices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usim_number' => ['required', 'string', 'max:255', 'unique:usims,usim_number'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'carrier' => ['nullable', 'string', 'max:100'],
            'site' => ['nullable', 'string', 'max:100'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'contract_date' => ['required', 'date'],
            'memo' => ['nullable', 'string'],
        ]);

        $data['status'] = Usim::STATUS_CONTRACT;

        Usim::create($data);

        return redirect()->route('usims.index')->with('success', '유심이 등록되었습니다.');
    }

    public function show(Usim $usim)
    {
        $usim->load(['customer', 'device', 'statusHistories.changedByUser']);

        return view('usims.show', compact('usim'));
    }

    public function edit(Usim $usim)
    {
        $customers = Customer::orderBy('name')->get();
        $devices = Device::where(function ($q) use ($usim) {
            $q->whereDoesntHave('usim')->orWhere('id', $usim->device_id);
        })->orderBy('model_name')->get();

        return view('usims.edit', compact('usim', 'customers', 'devices'));
    }

    public function update(Request $request, Usim $usim)
    {
        $data = $request->validate([
            'usim_number' => ['required', 'string', 'max:255', 'unique:usims,usim_number,' . $usim->id],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'carrier' => ['nullable', 'string', 'max:100'],
            'site' => ['nullable', 'string', 'max:100'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'status' => ['required', 'in:' . implode(',', Usim::STATUSES)],
            'contract_date' => ['nullable', 'date'],
            'suspended_date' => ['nullable', 'date'],
            'canceled_date' => ['nullable', 'date'],
            'memo' => ['nullable', 'string'],
        ]);

        if ($data['status'] === Usim::STATUS_SUSPENDED && empty($data['suspended_date'])) {
            $data['suspended_date'] = now()->toDateString();
        }

        if ($data['status'] === Usim::STATUS_CANCELED && empty($data['canceled_date'])) {
            $data['canceled_date'] = now()->toDateString();
        }

        $usim->update($data);

        return redirect()->route('usims.show', $usim)->with('success', '유심 정보가 저장되었습니다.');
    }

    public function destroy(Usim $usim)
    {
        $usim->delete();

        return redirect()->route('usims.index')->with('success', '유심이 삭제되었습니다.');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['keyword', 'status', 'site']);
        $filename = '유심목록_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new UsimsExport($filters), $filename);
    }

    public function importForm()
    {
        return view('usims.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new UsimsImport();
        Excel::import($import, $request->file('file'));

        $message = "{$import->created}건 등록, {$import->updated}건 업데이트되었습니다.";
        if (! empty($import->errors)) {
            $message .= ' (오류 ' . count($import->errors) . '건: ' . implode(', ', array_slice($import->errors, 0, 5)) . ')';
        }

        return redirect()->route('usims.index')->with('success', $message);
    }
}
