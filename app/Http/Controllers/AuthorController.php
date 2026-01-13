<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::where('name', 'like', '%' . request('search') . '%')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('authors.index', compact('authors'));
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
                'unique:authors,name',
            ],
        ], [
            'name.regex' => 'Author name may only contain letters, numbers, and spaces.',
            'name.unique' => 'This author already exists.',
        ]);

        $author = Author::create([
            'name' => strtolower(trim($validated['name'])),
        ]);

        return response()->json([
            'message'  => 'Author created successfully!',
            'author' => $author,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $author->update($request->only('name'));

        return response()->json([
            'message' => 'Author updated successfully',
            'author' => $author
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('authors.index')->with('success', 'Author deleted successfully');
    }
}
