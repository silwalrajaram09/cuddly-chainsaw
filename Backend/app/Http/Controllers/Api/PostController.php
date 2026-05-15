<?php
namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(Post::latest()->paginate(20));
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        if ($request->hasFile('default_photo')) {
            $data['default_photo'] = $request->file('default_photo')->store('posts', 'public');
        }

        $post = Post::create($data);
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();

        if ($request->hasFile('default_photo')) {
            if ($post->default_photo) Storage::disk('public')->delete($post->default_photo);
            $data['default_photo'] = $request->file('default_photo')->store('posts', 'public');
        }

        $post->update($data);
        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        if ($post->default_photo) Storage::disk('public')->delete($post->default_photo);
        $post->delete();
        return response()->json(null, 204);
    }
}