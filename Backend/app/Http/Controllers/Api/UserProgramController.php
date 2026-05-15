<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserProgramController extends Controller
{
    public function attach(Request $request, User $member)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $member->programs()->syncWithoutDetaching([$request->program_id]);
        return response()->json(['message' => 'Program attached']);
    }

    public function detach(User $member, $programId)
    {
        $member->programs()->detach($programId);
        return response()->json(null, 204);
    }
}