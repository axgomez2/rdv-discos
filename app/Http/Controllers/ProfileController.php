<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request)
{
    $user = $request->user()->load('addresses');
    return view('profile.edit', compact('user'));
}

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function storeAddress(Request $request)
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
                'is_default' => 'boolean',
            ]);

            $validatedData['user_id'] = $request->user()->id;
            $validatedData['is_default'] = $request->has('is_default');

            $address = Address::create($validatedData);

            Log::info('Address saved successfully:', $address->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully',
                'address' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save address: ' . $e->getMessage()
            ], 500);
        }
    }
}
