<?php

namespace App\Http\Controllers;

use App\Models\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $racks = Rack::where('name', 'like', '%' . request('search') . '%')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('racks.index', compact('racks'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:racks,name',
            'status' => 'nullable|in:active,inactive',
        ]);

        // 🔒 Force default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';

        $rack = Rack::create($validated);

        return response()->json([
            'message'  => 'Rack created successfully!',
            'rack' => $rack,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rack $rack)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rack $rack)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rack $rack)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:racks,name,' . $rack->id,
        ]);

        $rack->update($validated);

        return response()->json([
            'message'  => 'Rack updated successfully!',
            'rack' => $rack,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rack $rack)
    {
        $rack->delete(); // 👈 soft delete

        return redirect()->route('racks.index')->with('success', 'Rack deleted successfully');
    }
}
