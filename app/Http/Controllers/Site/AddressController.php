<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Store a newly created address in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Received address data:', $request->all());

        try {
            $validatedData = $request->validate([
                'type' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'number' => 'required|string|max:20',
                'complement' => 'nullable|string|max:255',
                'neighborhood' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|size:2',
                'zip_code' => 'required|string|size:8',
            ]);

            $validatedData['user_id'] = $request->user()->id;
            $validatedData['is_default'] = $request->has('is_default') ? true : false;

            // Se o endereço for definido como padrão, desmarcar outros endereços
            if ($validatedData['is_default']) {
                Address::where('user_id', $validatedData['user_id'])
                      ->where('is_default', true)
                      ->update(['is_default' => false]);
            }

            $address = Address::create($validatedData);

            Log::info('Address saved successfully:', $address->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Endereço adicionado com sucesso',
                'address' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Falha ao salvar endereço: ' . $e->getMessage()
            ], 500);
        }
    }
}
