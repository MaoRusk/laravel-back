<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndustrialParkRequest;
use App\Http\Requests\UpdateIndustrialParkRequest;
use App\Http\Resources\TenantsByParkResource;
use App\Models\BuildingAvailable;
use App\Models\IndustrialPark;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Enums\BuildingState;

class IndustrialParkController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:industrial-parks.show', only: ['show']),
            new Middleware('permission:industrial-parks.create', only: ['store']),
            new Middleware('permission:industrial-parks.update', only: ['update']),
            new Middleware('permission:industrial-parks.destroy', only: ['destroy']),
        ];
    }

    /**
     * @param Request $request
     * @return \App\Responses\ApiResponse
     */
    public function index(Request $request): \App\Responses\ApiResponse
    {
        $query = IndustrialPark::query();

        if ($request->has('market_id')) {
            $query->where('market_id', $request->input('market_id'));
        }

        if ($request->has('sub_market_id')) {
            $query->where('sub_market_id', $request->input('sub_market_id'));
        }

        $industrialParks = $query->get();

        return $this->success(data: $industrialParks);
    }

    /**
     * @param StoreIndustrialParkRequest $request
     * @return \App\Responses\ApiResponse
     */
    public function store(StoreIndustrialParkRequest $request): \App\Responses\ApiResponse
    {
        try {
            $industrialPark = IndustrialPark::create($request->validated());
            return $this->success('Industrial Park created successfully', $industrialPark);

        } catch (\Exception $e) {
            return $this->error('Error creating industrial park: ' . $e->getMessage(), status: 500);
        }
    }

    /**
     * @param IndustrialPark $industrialPark
     * @return \App\Responses\ApiResponse
     */
    public function show(IndustrialPark $industrialPark): \App\Responses\ApiResponse
    {
        return $this->success(data: $industrialPark);
    }

    /**
     * @param UpdateIndustrialParkRequest $request
     * @param IndustrialPark $industrialPark
     * @return \App\Responses\ApiResponse
     */
    public function update(UpdateIndustrialParkRequest $request, IndustrialPark $industrialPark): \App\Responses\ApiResponse
    {
        $industrialPark->update($request->validated());
        return $this->success(data: $industrialPark);
    }

    /**
     * @param IndustrialPark $industrialPark
     * @return \App\Responses\ApiResponse
     */
    public function destroy(IndustrialPark $industrialPark): \App\Responses\ApiResponse
    {
        try {

            if ($industrialPark->delete()) {
                return $this->success('Industrial Park deleted successfully', $industrialPark);
            }

            return $this->error('Industrial Park delete failed', status:422);

        } catch (\Exception $e) {
            return $this->error('Error deleting industrial park: ' . $e->getMessage(), status: 500);
        }
    }

    /**
     * @param IndustrialPark $industrialPark
     * @return ApiResponse
     */
    public function getTenantsByPark(IndustrialPark $industrialPark): ApiResponse
    {
       $tenantsByPark =  BuildingAvailable::select()->with('building', 'tenant')
            ->whereHas('building', function ($query) use ($industrialPark) {
                $query->where('industrial_park_id', $industrialPark->id);
            })
        ->where('building_state', BuildingState::ABSORPTION->value)
       ->get();
       return $this->success(data: TenantsByParkResource::collection($tenantsByPark));
    }
}
