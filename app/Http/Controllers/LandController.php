<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLandRequest;
use App\Http\Requests\StoreLandRequest;
use App\Http\Requests\UpdateLandRequest;
use App\Models\Land;
use App\Services\LandService;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Enums\LandParcelShape;
use App\Enums\LandZoning;
use App\Enums\LandsTypeBuyer;
use App\Enums\LandsServiceState;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:lands.show', only: ['show']),
            new Middleware('permission:lands.create', only: ['store']),
            new Middleware('permission:lands.update', only: ['update']),
            new Middleware('permission:lands.destroy', only: ['destroy']),
        ];
    }

    private LandService $landService;

    public function __construct(LandService $landService)
    {
        $this->landService = $landService;
    }

    /**
     * @return ApiResponse
     */
    public function index(IndexLandRequest $request): ApiResponse
    {
        $lands = $this->landService->filter($request->validated());
        return $this->success(data: $lands);
    }

    /**
     * @param Land $land
     * @return ApiResponse
     */
    public function show(Land $land): ApiResponse
    {
        $land = $this->landService->show($land);
        return $this->success(data: $land);
    }

    /**
     * @param StoreLandRequest $request
     * @return ApiResponse
     */
    public function store(StoreLandRequest $request): ApiResponse
    {
        $land = Land::create($request->validated());
        return $this->success('Land created successfully', $land);

    }

    /**
     * @param UpdateLandRequest $request
     * @param Land $land
     * @return ApiResponse
     */
    public function update(UpdateLandRequest $request, Land $land): ApiResponse
    {
        try{
            if($land->update($request->validated())) {
                return $this->success('Land updated successfully', $land);
            }
            return $this->error('Land update field', status: 422);

        }catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param Land $land
     * @return ApiResponse
     */
    public function destroy(Land $land): ApiResponse
    {
        try{
            if($land->delete()) {
                return $this->success('Land deleted successfully', $land);
            }
            return $this->error('Land delete field', status: 422);

        }catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @return ApiResponse
     */
    public function listParcelShape(): ApiResponse
    {
        return $this->success(data: LandParcelShape::array());
    }

    /**
     * @return ApiResponse
     */
    public function listZoning(): ApiResponse
    {
        return $this->success(data: LandZoning::array());
    }

    public function getLandTypeBuyer(): ApiResponse
    {
        return $this->success(data: LandsTypeBuyer::array());
    }
    public function getServiceState(): ApiResponse
    {
        return $this->success(data: LandsServiceState::array());
    }

}
