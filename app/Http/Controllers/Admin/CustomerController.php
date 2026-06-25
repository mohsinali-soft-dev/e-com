<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();
        $customers = Customer::withCount(['orders', 'sales'])
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('phone', 'like', "%$search%")->orWhere('email', 'like', "%$search%")))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function store(Request $request)
    {
        Customer::create($this->validated($request));

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders' => fn ($q) => $q->latest(), 'sales' => fn ($q) => $q->latest()]);

        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->withErrors(['customer' => 'Customers with orders cannot be deleted.']);
        }
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('customers')->ignore($customer)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers')->ignore($customer)],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
