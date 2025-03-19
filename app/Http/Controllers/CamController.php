<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCamRequest;
use App\Http\Requests\UpdateCamRequest;
use App\Models\Cam;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CamController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:cams.show', only: ['show']),
            new Middleware('permission:cams.create', only: ['store']),
            new Middleware('permission:cams.update', only: ['update']),
            new Middleware('permission:cams.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $cams = Cam::with([
            'industrialPark',
            'developer',
            'region',
            'market',
            'subMarket'
        ])->get();
        return $this->success(data: $cams);
    }

    /**
     * @param StoreCamRequest $request
     * @return ApiResponse
     */
    public function store(StoreCamRequest $request): ApiResponse
    {
        $cam = Cam::create($request->validated());
        return $this->success('Cam created successfully', $cam);
    }

    /**
     * @param Cam $cam
     * @return ApiResponse
     */
    public function show(Cam $cam): ApiResponse
    {
        return $this->success(data: $cam);
    }

    /**
     * @param UpdateCamRequest $request
     * @param Cam $cam
     * @return ApiResponse
     */
    public function update(UpdateCamRequest $request, Cam $cam): ApiResponse
    {
        try {
            if ($cam->update($request->validated())) {
                return $this->success('Cam updated successfully', $cam);
            }

            return $this->error('Cam update failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }

    /**
     * @param Cam $cam
     * @return ApiResponse
     */
    public function destroy(Cam $cam): ApiResponse
    {
        try {
            if ($cam->delete()) {
                return $this->success('Cam deleted successfully', $cam);
            }
            return $this->error('Cam delete failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }
}
