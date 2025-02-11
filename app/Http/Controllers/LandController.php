<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandRequest;
use App\Http\Requests\UpdateLandRequest;
use App\Models\Land;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Enums\LandParcelShape;
use App\Enums\LandZoning;

class LandController extends ApiController
{
    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $lands = Land::paginate(10);
        return $this->success(data: $lands);
    }

    /**
     * @param Land $land
     * @return ApiResponse
     */
    public function show(Land $land): ApiResponse
    {
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
            return $this->error('Land update field', status: 423);

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
            return $this->error('Land delete field', status: 423);

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

}
