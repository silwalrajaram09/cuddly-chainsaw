<?php
namespace App\Http\Controllers\Api;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\PageResource;

class PageController extends Controller
{
    public function index()
    {
        return PageResource::collection(Page::latest()->get());
    }

    public function show(Page $page)
    {
        return new PageResource($page);
    }

    public function store(StorePageRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('default_photo')) {
            $data['default_photo'] = $request->file('default_photo')->store('pages', 'public');
        }

        $page = Page::create($data);
        return new PageResource($page);
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $data = $request->validated();

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('default_photo')) {
            if ($page->default_photo) Storage::disk('public')->delete($page->default_photo);
            $data['default_photo'] = $request->file('default_photo')->store('pages', 'public');
        }

        $page->update($data);
        return new PageResource($page);
    }

    public function destroy(Page $page)
    {
        if ($page->default_photo) Storage::disk('public')->delete($page->default_photo);
        $page->delete();
        return response()->json(null, 204);
    }
}