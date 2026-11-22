<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderBy('is_primary', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_label'   => 'required|string|max:50',
            'full_address'    => 'required|string',
            'recipient_name'  => 'nullable|string|max:100',
            'recipient_phone' => 'nullable|string|max:20',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'notes'           => 'nullable|string',
            'is_primary'      => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi alamat gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // If this is set as primary or user has no other addresses, set as primary
        $isPrimary = $request->boolean('is_primary') || $user->addresses()->count() === 0;

        if ($isPrimary) {
            $user->addresses()->update(['is_primary' => false]);
        }

        $address = $user->addresses()->create([
            'address_label'   => $request->address_label,
            'recipient_name'  => $request->recipient_name ?? $user->name,
            'recipient_phone' => $request->recipient_phone ?? $user->phone,
            'full_address'    => $request->full_address,
            'latitude'        => $request->latitude ?? -6.200000,
            'longitude'       => $request->longitude ?? 106.816666,
            'notes'           => $request->notes,
            'is_primary'      => $isPrimary,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil ditambahkan.',
            'data'    => $address,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $address->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui.',
            'data'    => $address,
        ]);
    }

    public function setPrimary(Request $request, $id)
    {
        $user = $request->user();
        $user->addresses()->update(['is_primary' => false]);

        $address = $user->addresses()->findOrFail($id);
        $address->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat utama berhasil diatur.',
            'data'    => $address,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus.',
        ]);
    }
}

