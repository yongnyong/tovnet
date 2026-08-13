<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()->withCount('usims');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $customers = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', '고객이 등록되었습니다.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['usims.device']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', '고객 정보가 수정되었습니다.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->usims()->exists()) {
            return back()->with('error', '연결된 유심이 있는 고객은 삭제할 수 없습니다.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', '고객이 삭제되었습니다.');
    }
}
