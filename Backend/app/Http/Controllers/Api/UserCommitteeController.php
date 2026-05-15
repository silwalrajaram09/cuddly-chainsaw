<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserCommitteeController extends Controller
{
    public function attach(Request $request, User $member)
    {
        $request->validate([
            'committee_id' => 'required|exists:committees,id',
            'order_no'     => 'nullable|numeric',
        ]);

        $member->committees()->syncWithoutDetaching([
            $request->committee_id => ['order_no' => $request->order_no ?? 0],
        ]);

        return response()->json(['message' => 'Committee attached']);
    }

    public function updateOrder(Request $request, User $member, $committeeId)
    {
        $request->validate(['order_no' => 'required|numeric']);

        $member->committees()->updateExistingPivot($committeeId, [
            'order_no' => $request->order_no,
        ]);

        return response()->json(['message' => 'Order updated']);
    }

    public function detach(User $member, $committeeId)
    {
        $member->committees()->detach($committeeId);
        return response()->json(null, 204);
    }
}