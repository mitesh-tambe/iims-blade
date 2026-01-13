<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publications = Publication::where('name', 'like', '%' . request('search') . '%')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('publications.index', compact('publications'));
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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s]+$/',
                'unique:publications,name',
            ],
        ], [
            'name.regex' => 'Publication name may only contain letters, numbers, and spaces.',
            'name.unique' => 'This publication already exists.',
        ]);

        $publication = Publication::create([
            'name' => strtolower(trim($validated['name'])), // ✅ store lowercase
        ]);

        return response()->json([
            'message'  => 'Publication created successfully!',
            'publication' => $publication,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publication $publication)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publication $publication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publication $publication)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $publication->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Publication updated successfully!',
            'publication'  => $publication,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication)
    {
        $publication->delete();

        return redirect()->route('publications.index')
            ->with('success', 'Publication deleted successfully!');
    }
}
