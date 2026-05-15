<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use App\Http\Requests\StoreCommitteeRequest;
use App\Http\Requests\UpdateCommitteeRequest;
use App\Http\Resources\CommitteeResource;

class CommitteeController extends Controller
{
    public function index()
    {
        $committees = Committee::with('children')
            ->whereNull('parent_id')
            ->get();
        return CommitteeResource::collection($committees);
    }

    public function show(Committee $committee)
    {
        return new CommitteeResource($committee->load('children', 'parent'));
    }

    public function store(StoreCommitteeRequest $request)
    {
        $committee = Committee::create($request->validated());
        return new CommitteeResource($committee);
    }

    public function update(UpdateCommitteeRequest $request, Committee $committee)
    {
        $committee->update($request->validated());
        return new CommitteeResource($committee);
    }

    // public function destroy(Committee $committee)
    // {
    //     $committeeName = $committee->name;

    //     $committee->delete();

    //     return response()->json([
    //         'message' => "{$committeeName} deleted successfully"
    //     ]);
    // }

    public function destroy(Committee $committee)
    {
        $committee->delete();

        return response()->noContent();
    }
}