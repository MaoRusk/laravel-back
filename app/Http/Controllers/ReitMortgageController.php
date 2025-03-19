<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexReitMortgageRequest;
use App\Http\Requests\StoreReitMortgageRequest;
use App\Http\Requests\UpdateReitMortgageRequest;
use App\Models\ReitMortgage;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReitMortgageController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:reit-mortgage.show', only: ['show']),
            new Middleware('permission:reit-mortgage.create', only: ['store']),
            new Middleware('permission:reit-mortgage.update', only: ['update']),
            new Middleware('permission:reit-mortgage.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(IndexReitMortgageRequest $request): ApiResponse
    {
        $reitsMortgage = ReitMortgage::filter($request->validated());
        return $this->success(data: $reitsMortgage);
    }

    /**
     * @param StoreReitMortgageRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitMortgageRequest $request): ApiResponse
    {
        $reitMortgage = ReitMortgage::create($request->validated());
        return $this->success('Reit Mortgage created successfully', $reitMortgage);
    }

    /**
     * @param ReitMortgage $reitMortgage
     * @return ApiResponse
     */
    public function show(ReitMortgage $reitMortgage): ApiResponse
    {
        $reitMortgage->load(['reit', 'reitType']);
        return $this->success(data: $reitMortgage);
    }

    /**
     * @param UpdateReitMortgageRequest $request
     * @param ReitMortgage $reitMortgage
     * @return ApiResponse
     */
    public function update(UpdateReitMortgageRequest $request, ReitMortgage $reitMortgage): ApiResponse
    {
        try {
            if($reitMortgage->update($request->validated())) {
                return $this->success('Reit Mortgage updated successfully', $reitMortgage);
            }
            return $this->error('Reit Mortgage updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param ReitMortgage $reitMortgage
     * @return ApiResponse
     */
    public function destroy(ReitMortgage $reitMortgage): ApiResponse
    {
        try {
            if($reitMortgage->delete()) {
                return $this->success('Reit Mortgage deleted successfully', $reitMortgage);
            }
            return $this->error('Reit Mortgage deleted field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
