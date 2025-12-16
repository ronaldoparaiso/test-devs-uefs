<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Post::with(['user', 'tags'])->get(),
            'message' => 'Posts retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'tags' => 'sometimes|array',
                'tags.*' => 'exists:tags,id',
            ]);

            $post = Post::create([
                'user_id' => $validated['user_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
            ]);

            if (isset($validated['tags'])) {
                $post->tags()->attach($validated['tags']);
            }

            return response()->json([
                'success' => true,
                'data' => $post->load(['user', 'tags']),
                'message' => 'Post created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json([
            'success' => true,
            'data' => $post->load(['user', 'tags']),
            'message' => 'Post retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'sometimes|required|exists:users,id',
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'tags' => 'sometimes|array',
                'tags.*' => 'exists:tags,id',
            ]);

            $post->update($validated);

            if (isset($validated['tags'])) {
                $post->tags()->sync($validated['tags']);
            }

            return response()->json([
                'success' => true,
                'data' => $post->load(['user', 'tags']),
                'message' => 'Post updated successfully'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
