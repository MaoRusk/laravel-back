<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexReitAnnualRequest;
use App\Http\Requests\StoreReitAnnualRequest;
use App\Http\Requests\UpdateReitAnnualRequest;
use App\Models\ReitAnnual;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReitAnnualController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:reit-annual.show', only: ['show']),
            new Middleware('permission:reit-annual.create', only: ['store']),
            new Middleware('permission:reit-annual.update', only: ['update']),
            new Middleware('permission:reit-annual.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(IndexReitAnnualRequest $request): ApiResponse
    {
        $reitAnnuals = ReitAnnual::filter($request->validated());
        return $this->success(data: $reitAnnuals);
    }

    /**
     * @param StoreReitAnnualRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitAnnualRequest $request): ApiResponse
    {
        $reitAnnual = ReitAnnual::create($request->validated());
        return $this->success('Reit annual created successfully', $reitAnnual);
    }

    /**
     * @param ReitAnnual $reitAnnual
     * @return ApiResponse
     */
    public function show(ReitAnnual $reitAnnual): ApiResponse
    {
        return $this->success(data: $reitAnnual);
    }

    /**
     * @param UpdateReitAnnualRequest $request
     * @param ReitAnnual $reitAnnual
     * @return ApiResponse
     */
    public function update(UpdateReitAnnualRequest $request, ReitAnnual $reitAnnual): ApiResponse
    {
        try {
            if($reitAnnual->update($request->validated())) {
                return $this->success('Reit annual updated successfully', $reitAnnual);
            }
            return  $this->error('Reit annual updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param ReitAnnual $reitAnnual
     * @return ApiResponse
     */
    public function destroy(ReitAnnual $reitAnnual): ApiResponse
    {
        try{
            if($reitAnnual->delete()) {
                return $this->success('Reit annual deleted successfully', $reitAnnual);
            }
            return $this->error('Reit annual deleted filed', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
