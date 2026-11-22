<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * List services with optional category filter
     */
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if (!$request->user() || !$request->user()->isAdmin()) {
            $query->where('is_active', true);
        }

        $services = $query->orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar layanan berhasil diambil.',
            'data'    => $services,
        ]);
    }

    /**
     * Service detail
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $service,
        ]);
    }

    /**
     * Admin create service
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'category'        => 'required|in:kiloan,satuan,dry_clean,express',
            'price_per_unit'  => 'required|numeric|min:0',
            'unit'            => 'required|string|max:20',
            'estimated_hours' => 'required|integer|min:1',
            'description'     => 'nullable|string',
            'icon'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data layanan gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $service = Service::create([
            'name'            => $request->name,
            'slug'            => Str::slug($request->name) . '-' . Str::random(4),
            'category'        => $request->category,
            'price_per_unit'  => $request->price_per_unit,
            'unit'            => $request->unit,
            'estimated_hours' => $request->estimated_hours,
            'description'     => $request->description,
            'icon'            => $request->icon ?? 'sparkles',
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Layanan baru berhasil ditambahkan.',
            'data'    => $service,
        ], 201);
    }

    /**
     * Admin update service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|required|string|max:255',
            'category'        => 'sometimes|required|in:kiloan,satuan,dry_clean,express',
            'price_per_unit'  => 'sometimes|required|numeric|min:0',
            'unit'            => 'sometimes|required|string|max:20',
            'estimated_hours' => 'sometimes|required|integer|min:1',
            'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data layanan berhasil diperbarui.',
            'data'    => $service,
        ]);
    }

    /**
     * Admin delete service
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dihapus.',
        ]);
    }
}

