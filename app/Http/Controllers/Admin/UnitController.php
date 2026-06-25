<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(15);
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'short_name' => 'required|string|max:20',
            'type' => 'required|string|max:20',
            'decimal_places' => 'required|integer|min:0|max:3',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Unit::create($data);

        return redirect()->route('admin.units.index')->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:20',
            'type' => 'required|string|max:20',
            'decimal_places' => 'required|integer|min:0|max:3',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $unit->update($data);

        return redirect()->route('admin.units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->exists()) {
            return back()->with('success', 'Unit cannot be deleted because it is in use.');
        }

        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'Unit deleted successfully.');
    }
}
