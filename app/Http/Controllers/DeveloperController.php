<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeveloperRequest;
use App\Http\Requests\UpdateDeveloperRequest;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;

class DeveloperController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \App\Responses\ApiResponse
    {
        return $this->success(data: DeveloperResource::collection(Developer::all()));
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
