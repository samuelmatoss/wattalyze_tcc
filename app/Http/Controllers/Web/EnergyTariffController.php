<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnergyTariff;
use Illuminate\Support\Facades\Auth;

class EnergyTariffController extends Controller
{
    public function index()
    {
        $tariffs = EnergyTariff::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('tariffs.index', compact('tariffs'));
    }

    public function create()
    {
        return view('tariffs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'tariff_type' => 'nullable|string|max:255',

            'bracket1_min' => 'required|numeric|min:0',
            'bracket1_max' => 'nullable|numeric|gt:bracket1_min',
            'bracket1_rate' => 'required|numeric|min:0',

            'bracket2_min' => 'nullable|numeric|gt:bracket1_max',
            'bracket2_max' => 'nullable|numeric|gt:bracket2_min',
            'bracket2_rate' => 'nullable|numeric|min:0',

            'bracket3_min' => 'nullable|numeric|gt:bracket2_max',
            'bracket3_max' => 'nullable|numeric|gt:bracket3_min',
            'bracket3_rate' => 'nullable|numeric|min:0',

            'tax_rate' => 'nullable|numeric|min:0',

            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',

            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['user_id'] = Auth::id();

        EnergyTariff::create($validated);

        return redirect()->route('tariffs.index')->with('success', 'Tarifa criada com sucesso!');
    }

    public function edit(EnergyTariff $tariff)
    {

        return view('tariffs.edit', compact('tariff'));
    }

    public function update(Request $request, EnergyTariff $tariff)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'tariff_type' => 'nullable|string|max:255',

            'bracket1_min' => 'required|numeric|min:0',
            'bracket1_max' => 'nullable|numeric|gt:bracket1_min',
            'bracket1_rate' => 'required|numeric|min:0',

            'bracket2_min' => 'nullable|numeric|gt:bracket1_max',
            'bracket2_max' => 'nullable|numeric|gt:bracket2_min',
            'bracket2_rate' => 'nullable|numeric|min:0',

            'bracket3_min' => 'nullable|numeric|gt:bracket2_max',
            'bracket3_max' => 'nullable|numeric|gt:bracket3_min',
            'bracket3_rate' => 'nullable|numeric|min:0',

            'tax_rate' => 'nullable|numeric|min:0',

            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',

            'is_active' => 'boolean',
        ]);

        $tariff->update($validated);

        return redirect()->route('tariffs.index')->with('success', 'Tarifa atualizada com sucesso!');
    }

    public function destroy(EnergyTariff $tariff)
    {

        $tariff->delete();

        return redirect()->route('tariffs.index')->with('success', 'Tarifa excluída com sucesso!');
    }
}
