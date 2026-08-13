<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::query()->with('usim');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('model_name', 'like', "%{$keyword}%")
                    ->orWhere('serial_number', 'like', "%{$keyword}%");
            });
        }

        $devices = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'model_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number'],
            'memo' => ['nullable', 'string'],
        ]);

        Device::create($data);

        return redirect()->route('devices.index')->with('success', '기기가 등록되었습니다.');
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'model_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number,' . $device->id],
            'memo' => ['nullable', 'string'],
        ]);

        $device->update($data);

        return redirect()->route('devices.index')->with('success', '기기 정보가 수정되었습니다.');
    }

    public function destroy(Device $device)
    {
        if ($device->usim()->exists()) {
            return back()->with('error', '유심에 연결된 기기는 삭제할 수 없습니다.');
        }

        $device->delete();

        return redirect()->route('devices.index')->with('success', '기기가 삭제되었습니다.');
    }
}
