<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Landing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Landing::where('user_id', $request->user()->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['landingData' => 'required|array']);

        // Create a new landing instance with user_id
        $landing = new Landing();
        $landing->landingData = $validated['landingData'];
        $landing->user_id = $request->user()->id;
        $landing->save();

        return response()->json($landing, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Landing $landing)
    {
        abort_unless($landing->user_id == $request->user()->id, 403);
        return response()->json($landing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Landing $landing)
    {
        abort_unless($landing->user_id == $request->user()->id, 403);
        $landing->update($request->validate(['landingData' => 'required|array']));

        return response()->json($landing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Landing $landing)
    {
        abort_unless($landing->user_id == $request->user()->id, 403);
        $landing->delete();

        return response()->json(null, 204);
    }

    /**
     * show user landing based on id.
     */
    public function showUserLanding(Request $request, $user_id)
    {
        abort_unless((string) $user_id === (string) $request->user()->id, 403);
        // Retrieve the user's landing based on the user ID
        $landing = Landing::where('user_id', $user_id)->get();

        if ($landing->isEmpty()) {
            return response()->json(['message' => 'User landing not found'], 404);
        }

        return response()->json($landing);
    }

    /**
     * Upload media for a landing and return the URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media' => 'required|file|mimes:jpeg,png|max:2048', // Adjust validation rules as needed
        ], [
            'media.required' => 'انتخاب فایل ضروری میباشد.',
            'media.file' => 'فایل آپلود شده مشکل دارد.',
            'media.mimes' => 'نوع فایل آپلود شده باید : jpeg, png باشد',
            'media.max' => 'حجم فایل انتخاب شده نباید بیشتر از 2 مگابایت باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Handle media upload
        if ($request->hasFile('media')) {
            $file = $request->file('media');

            // Store the file in the 'public' disk
            $path = $file->store('uploads', 'public');

            // Get the URL of the uploaded media
            $url = Storage::disk('public')->url($path);

            // Remove the protocol and hostname part from the URL
            $parsedUrl = parse_url($url);
            $relativeUrl = $parsedUrl['path'];

            return response()->json(['url' => $relativeUrl], JsonResponse::HTTP_OK);
        }

        return response()->json(['message' => 'No file uploaded'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
