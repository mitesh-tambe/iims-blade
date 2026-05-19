<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $vendors = Vendor::all();
        // return view('vendors.index', compact('vendors'));
        $vendors = Vendor::where('name', 'like', '%' . request('search') . '%')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('vendors.index', compact('vendors'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // ✅ Validation
            $validated = $request->validate(
                [
                    'name' => 'required|string|max:255|unique:vendors,name',
                    'phone' => 'required|digits_between:1,12',
                    'email' => 'nullable|email|max:255|unique:vendors,email',
                    'address' => 'nullable|string',
                    'pan_no' => 'nullable|string|max:255',
                    'gst_no' => 'nullable|string|max:255',
                ],
                [
                    'phone.digits_between' => 'The phone number cannot be more than 12 digits.',
                ]
            );

            // ✅ Create Vendor
            $vendor = Vendor::create($validated);

            // ✅ Return JSON (for your fetch)
            return response()->json([
                'message' => 'Vendor created successfully',
                'vendor' => $vendor
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 🔴 Send validation errors properly
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // 🔴 Catch unexpected errors
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        try {
            // ✅ Validation
            $validated = $request->validate(
                [
                    'name' => 'required|string|max:255|unique:vendors,name,' . $vendor->id,
                    'phone' => 'required|digits_between:1,12',
                    'email' => 'nullable|email|max:255|unique:vendors,email,' . $vendor->id,
                    'address' => 'nullable|string',
                    'pan_no' => 'nullable|string|max:255',
                    'gst_no' => 'nullable|string|max:255',
                ],
                [
                    'phone.digits_between' => 'The phone number cannot be more than 12 digits.',
                ]
            );

            // ✅ Update Vendor
            $vendor->update($validated);

            // ✅ Return JSON (for your fetch)
            return response()->json([
                'message' => 'Vendor updated successfully',
                'vendor' => $vendor
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 🔴 Send validation errors properly
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // 🔴 Catch unexpected errors
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();
            return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
