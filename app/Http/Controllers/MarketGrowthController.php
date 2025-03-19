<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarketGrowthRequest;
use App\Http\Requests\UpdateMarketGrowthRequest;
use App\Models\MarketGrowth;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MarketGrowthController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:market-growths.show', only: ['show']),
            new Middleware('permission:market-growths.create', only: ['store']),
            new Middleware('permission:market-growths.update', only: ['update']),
            new Middleware('permission:market-growths.destroy', only: ['destroy']),
        ];
    }
    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $marketGrowths = MarketGrowth::with('building', 'owner', 'developer', 'builder', 'industrialPark', 'region', 'market', 'subMarket')->get();
        return $this->success(data: $marketGrowths);
    }

    /**
     * @param StoreMarketGrowthRequest $request
     * @return ApiResponse
     */
    public function store(StoreMarketGrowthRequest $request): ApiResponse
    {
        $cam = MarketGrowth::create($request->validated());
        return $this->success('Market Growth created successfully', $cam);
    }

    /**
     * @param MarketGrowth $marketGrowth
     * @return ApiResponse
     */
    public function show(MarketGrowth $marketGrowth): ApiResponse
    {
        return $this->success(data: $marketGrowth);
    }

    /**
     * @param UpdateMarketGrowthRequest $request
     * @param MarketGrowth $marketGrowth
     * @return ApiResponse
     */
    public function update(UpdateMarketGrowthRequest $request, MarketGrowth $marketGrowth): ApiResponse
    {
        try {
            if ($marketGrowth->update($request->validated())) {
                return $this->success('Market Growth updated successfully', $marketGrowth);
            }

            return $this->error('Market Growth update failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }

    /**
     * @param MarketGrowth $marketGrowth
     * @return ApiResponse
     */
    public function destroy(MarketGrowth $marketGrowth): ApiResponse
    {
        try {
            if ($marketGrowth->delete()) {
                return $this->success('Market Growth deleted successfully', $marketGrowth);
            }
            return $this->error('Market Growth delete failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }
}
