<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;

class ComponentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Component::where('user_id', $request->user()->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'componentData' => 'required|array',
        ]);
        $component = new Component($validated);
        $component->user_id = $request->user()->id;
        $component->save();

        return response()->json($component, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Component $component)
    {
        abort_unless($component->user_id == $request->user()->id, 403);
        return response()->json($component);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Component $component)
    {
        abort_unless($component->user_id == $request->user()->id, 403);
        $component->update($request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'componentData' => 'sometimes|required|array',
        ]));

        return response()->json($component);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Component $component)
    {
        abort_unless($component->user_id == $request->user()->id, 403);
        $component->delete();

        return response()->json(null, 204);
    }

}
