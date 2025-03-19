<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReitsTimelineRequest;
use App\Http\Requests\UpdateReitsTimelineRequest;
use App\Models\ReitsTimeline;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Responses\ApiResponse;

class ReitsTimelineController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:reits-timeline.show', only: ['show']),
            new Middleware('permission:reits-timeline.create', only: ['store']),
            new Middleware('permission:reits-timeline.update', only: ['update']),
            new Middleware('permission:reits-timeline.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $reitsTimeline = ReitsTimeline::all();
        return $this->success(data: $reitsTimeline);
    }

    /**
     * @param StoreReitsTimelineRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitsTimelineRequest $request): ApiResponse
    {
        $reitTimeline = ReitsTimeline::create($request->validated());
        return $this->success('Reit Timeline created successfully', $reitTimeline);
    }

    /**
     * @param ReitsTimeline $reitTimeline
     * @return ApiResponse
     */
    public function show(ReitsTimeline $reitTimeline): ApiResponse
    {
        $reitTimeline->load(['reit']);
        return $this->success(data: $reitTimeline);
    }

    /**
     * @param UpdateReitsTimelineRequest $request
     * @param ReitsTimeline $reitTimeline
     * @return ApiResponse
     */
    public function update(UpdateReitsTimelineRequest $request, ReitsTimeline $reitTimeline): ApiResponse
    {
        try {
            if($reitTimeline->update($request->validated())) {
                return $this->success('Reit Timeline updated successfully', $reitTimeline);
            }
            return $this->error('Reit Timeline updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param ReitsTimeline $reitTimeline
     * @return ApiResponse
     */
    public function destroy(ReitsTimeline $reitTimeline): ApiResponse
    {
        try {
            if($reitTimeline->delete()) {
                return $this->success('Reit Timeline deleted successfully', $reitTimeline);
            }
            return $this->error('Reit Timeline deleted field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
