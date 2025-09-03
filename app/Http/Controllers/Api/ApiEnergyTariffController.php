<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnergyTariff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApiEnergyTariffController extends Controller
{
    /**
     * Get all energy tariffs for authenticated user
     */
    public function index(): JsonResponse
    {
        $tariffs = EnergyTariff::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json(['tariffs' => $tariffs]);
    }

    /**
     * Create a new energy tariff
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $validated['user_id'] = Auth::id();

        $tariff = EnergyTariff::create($validated);

        return response()->json([
            'message' => 'Tarifa criada com sucesso!',
            'tariff' => $tariff
        ], 201);
    }

    /**
     * Get a single energy tariff
     */
    public function show(EnergyTariff $tariff): JsonResponse
    {
        // Check authorization
        if ($tariff->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['tariff' => $tariff]);
    }

    /**
     * Update an energy tariff
     */
    public function update(Request $request, EnergyTariff $tariff): JsonResponse
    {
        // Check authorization
        if ($tariff->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');

        $tariff->update($validated);

        return response()->json([
            'message' => 'Tarifa atualizada com sucesso!',
            'tariff' => $tariff
        ]);
    }

    /**
     * Delete an energy tariff
     */
    public function destroy(EnergyTariff $tariff): JsonResponse
    {
        // Check authorization
        if ($tariff->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tariff->delete();

        return response()->json(['message' => 'Tarifa excluída com sucesso!']);
    }
}