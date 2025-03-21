<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertToAbsorptionRequest;
use App\Http\Requests\IndexBuildingsAvailableRequest;
use App\Http\Requests\StoreBuildingsAvailableRequest;
use App\Http\Requests\UpdateBuildingsAvailableRequest;
use App\Models\Building;
use App\Models\BuildingAvailable;
use App\Services\BuildingsAvailableService;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Enums\BuildingState;

class BuildingsAvailableController extends ApiController
{
    /**
     * @param Request $request
     * @param Building $building
     * @return ApiResponse
     */

    private BuildingsAvailableService $buildingAvailableService;

    public function __construct(BuildingsAvailableService $buildingAvailableService)
    {
        $this->buildingAvailableService = $buildingAvailableService;
    }

    public function index(IndexBuildingsAvailableRequest $request, Building $building): ApiResponse
    {
        $validated = $request->validated();
        $availabilities = $this->buildingAvailableService->filterAvailable($validated, $building->id);

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }

        return $this->success(data: $availabilities);
    }


    /**
     * @param StoreBuildingsAvailableRequest $request
     * @param Building $building
     * @return ApiResponse
     */
    public function store(StoreBuildingsAvailableRequest $request, Building $building): ApiResponse
    {
        $validated = $request->validated();
        $validated['building_id'] = $building->id;
        $validated['building_state'] = BuildingState::AVAILABILITY;

        if ($validated['sqftToM2'] ?? false) {
            $validated = $this->buildingAvailableService->convertMetrics($validated);
        }
        if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
            $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
        }
        if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
            $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
        }

        $availability = $this->buildingAvailableService->create($validated);

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }

        return $this->success('Building Available created successfully', $availability);
    }

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAvailable
     * @return ApiResponse
     */
    public function show(Building $building, BuildingAvailable $buildingAvailable): ApiResponse
    {
        if ($buildingAvailable->building_id !== $building->id) {
            return $this->error('Building Available not found for this Building', ['error_code' => 404]);
        }

        if ($buildingAvailable->building_state !== BuildingState::AVAILABILITY->value) {
            return $this->error('Invalid building state', ['error_code' => 403]);
        }

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
           $building->above_market_tis = explode(',', $building->above_market_tis);
       }

        return $this->success(data: $buildingAvailable);
    }


    /**
     * @param UpdateBuildingsAvailableRequest $request
     * @param Building $building
     * @param BuildingAvailable $buildingAvailable
     * @return ApiResponse
     */
    public function update(UpdateBuildingsAvailableRequest $request, Building $building, BuildingAvailable $buildingAvailable): ApiResponse
    {
        if ($buildingAvailable->building_id !== $building->id) {
            return $this->error('Building Available not found for this Building', ['error_code' => 404]);
        }
        if ($buildingAvailable->building_state !== BuildingState::AVAILABILITY->value) {
            return $this->error('Invalid building state', ['error_code' => 403]);
        }

        $validated = $request->validated();
        $validated['building_id'] = $building->id;
        $validated['building_state'] = 'Availability';
        try {
            if ($validated['sqftToM2'] ?? false) {
                $validated = $this->buildingAvailableService->convertMetrics($validated);
            }
            if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
                $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
            }
            if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
                $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
            }
            $building = $this->buildingAvailableService->update($buildingAvailable, $validated);
            if (!empty($building->fire_protection_system)) {
                $building->fire_protection_system = explode(',', $building->fire_protection_system);
            }
            if (!empty($building->above_market_tis)) {
                $building->above_market_tis = explode(',', $building->above_market_tis);
            }
            return $this->success('Building Available updated successfully', $buildingAvailable);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAvailable
     * @return ApiResponse
     */
    public function destroy(Building $building, BuildingAvailable $buildingAvailable): ApiResponse
    {
        if ($buildingAvailable->building_id !== $building->id) {
            return $this->error('Building Available not found for this Building', ['error_code' => 404]);
        }

        if ($buildingAvailable->building_state !== BuildingState::AVAILABILITY->value) {
            return $this->error('Invalid building state', ['error_code' => 403]);
        }

        try {
            if ($buildingAvailable->delete()) {
                return $this->success('Building Available deleted successfully', $buildingAvailable);
            }
            return $this->error('Building Available delete failed', ['error_code' => 423]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['error_code' => 500]);
        }
    }

    /**
     * @param ConvertToAbsorptionRequest $request
     * @param Building $building
     * @param BuildingAvailable $buildingAvailable
     * @return ApiResponse
     */
    public function toAbsorption(ConvertToAbsorptionRequest $request, Building $building, BuildingAvailable $buildingAvailable): ApiResponse
    {
        $validated = $request->validated();
        if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
            $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
        }
        if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
            $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
        }
        $result = $this->buildingAvailableService->convertToAbsorption($validated, $building->id, $buildingAvailable->id);
        if (!$result['success']) {
            return $this->error($result['message'], ['error_code' => $result['code']]);
        }
        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }

        return $this->success(data: $result['data']);
    }


}
