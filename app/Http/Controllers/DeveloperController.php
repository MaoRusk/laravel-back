<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeveloperRequest;
use App\Http\Requests\UpdateDeveloperRequest;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DeveloperController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:developers.show', only: ['show']),
            new Middleware('permission:developers.create', only: ['store']),
            new Middleware('permission:developers.update', only: ['update']),
            new Middleware('permission:developers.destroy', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): \App\Responses\ApiResponse
    {
        $filters = $request->only(['is_owner', 'is_builder', 'is_developer', 'market', 'submarket']);
        $developers = Developer::query()->filter($filters)->get();
        return $this->success(data: DeveloperResource::collection($developers));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeveloperRequest $request): \App\Responses\ApiResponse
    {
        $developer = Developer::create($request->validated());

        return $this->success('Developer created successfully', new DeveloperResource($developer));
    }

    /**
     * Display the specified resource.
     */
    public function show(Developer $developer): \App\Responses\ApiResponse
    {
        return $this->success(data: new DeveloperResource($developer));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeveloperRequest $request, Developer $developer): \App\Responses\ApiResponse
    {
        try {
            if ($developer->update($request->validated())) {
                return $this->success('Developer updated successfully', new DeveloperResource($developer));
            }

            return $this->error('Developer update failed', 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Developer $developer): \App\Responses\ApiResponse
    {
        try {
            if ($developer->delete()) {
                return $this->success('Developer deleted successfully', new DeveloperResource($developer));
            }
            return $this->error('Developer delete failed', 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }

    }
}
