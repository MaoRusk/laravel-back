<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndustryRequest;
use App\Http\Requests\UpdateIndustryRequest;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Models\Industry;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IndustryController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:industries.show', only: ['show']),
            new Middleware('permission:industries.create', only: ['store']),
            new Middleware('permission:industries.update', only: ['update']),
            new Middleware('permission:industries.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $industries = Industry::all();
        return $this->success(data: $industries);
    }

    /**
     * @param StoreIndustryRequest $request
     * @return ApiResponse
     */
    public function store(StoreIndustryRequest $request): ApiResponse
    {
        $industry = Industry::create($request->validated());
        return $this->success('Industry created successfully', $industry);
    }

    /**
     * @param UpdateIndustryRequest $request
     * @param Industry $industry
     * @return ApiResponse
     */
    public function update(UpdateIndustryRequest $request, Industry $industry): ApiResponse
    {
        try {
            if ($industry->update($request->validated())) {
                return $this->success('Industry updated successfully', $industry);
            }

            return $this->error('Industry update failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }

    /**
     * @param Industry $industry
     * @return ApiResponse
     */
    public function destroy(Industry $industry): ApiResponse
    {
        try {
            if ($industry->delete()) {
                return $this->success('Industry deleted successfully', $industry);
            }
            return $this->error('Industry delete failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }

}
