<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('committees', 'programs')->paginate(20);
        return UserResource::collection($users);
    }

    public function show(User $member)
    {
        return new UserResource($member->load('committees', 'programs'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['full_name']) . '-' . Str::random(4);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('users', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $member = User::create($data);
        return new UserResource($member);
    }

    public function update(UpdateUserRequest $request, User $member)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($member->photo_path) Storage::disk('public')->delete($member->photo_path);
            $data['photo_path'] = $request->file('photo')->store('users', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $member->update($data);
        return new UserResource($member);
    }

    public function destroy(User $member)
    {
        if ($member->photo_path) Storage::disk('public')->delete($member->photo_path);
        $member->delete();
        return response()->json(null, 204);
    }
}