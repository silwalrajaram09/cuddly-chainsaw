<?php
namespace App\Http\Controllers\Api;

use App\Models\Program;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Http\Resources\ProgramResource;

class ProgramController extends Controller
{
    public function index()
    {
        return ProgramResource::collection(Program::latest()->paginate(20));
    }

    public function show(Program $program)
    {
        return new ProgramResource($program->load('members'));
    }

    public function store(StoreProgramRequest $request)
    {
        $program = Program::create($request->validated());
        return new ProgramResource($program);
    }

    public function update(UpdateProgramRequest $request, Program $program)
    {
        $program->update($request->validated());
        return new ProgramResource($program);
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json(null, 204);
    }
}