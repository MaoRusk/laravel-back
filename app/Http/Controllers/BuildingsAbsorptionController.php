<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertToAvailableRequest;
use App\Http\Requests\StoreBuildingsAbsorptionRequest;
use App\Http\Requests\UpdateBuildingAbsorptionDraftRequest;
use App\Http\Requests\UpdateBuildingsAbsorptionRequest;
use App\Models\Building;
use App\Models\BuildingAvailable;
use App\Services\BuildingsAvailableService;
use App\Responses\ApiResponse;
use App\Enums\BuildingState;
use App\Http\Requests\IndexBuildingsAbsorptionRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Enums\BuildingStatus;
use Illuminate\Support\Arr;

class BuildingsAbsorptionController extends ApiController implements HasMiddleware
{
    private BuildingsAvailableService $buildingAvailableService;

    public static function middleware()
    {
        return [
            new Middleware('permission:buildings.absorption.index', only: ['index']),
            new Middleware('permission:buildings.absorption.show', only: ['show']),
            new Middleware('permission:buildings.absorption.create', only: ['store']),
            new Middleware('permission:buildings.absorption.update', only: ['update']),
            new Middleware('permission:buildings.absorption.destroy', only: ['destroy']),
            new Middleware('permission:buildings.availability.to-available', only: ['toAvailable']),
        ];
    }

    public function __construct(BuildingsAvailableService $buildingAvailableService)
    {
        $this->buildingAvailableService = $buildingAvailableService;
    }

    /**
     * @param IndexBuildingsAbsorptionRequest $request
     * @param Building $building
     * @return ApiResponse
     */
    public function index(IndexBuildingsAbsorptionRequest $request, Building $building): ApiResponse
    {
        $validated = $request->validated();
        $absorptions = $this->buildingAvailableService->filterAbsorption($validated, $building->id);

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }

        return $this->success(data: $absorptions);
    }


    /**
     * @param StoreBuildingsAbsorptionRequest $request
     * @param Building $building
     * @return ApiResponse
     */
    public function store(StoreBuildingsAbsorptionRequest $request, Building $building): ApiResponse
    {
        $validated = $request->validated();
        $validated['building_id'] = $building->id;
        $validated['building_state'] = BuildingState::ABSORPTION;

        if ($validated['sqftToM2'] ?? false) {
            $validated = $this->buildingAvailableService->convertMetrics($validated);
        }

        if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
            $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
        }
        if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
            $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
        }

        $absorption = $this->buildingAvailableService->create($validated);

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }

        return $this->success('Building Absorption created successfully', $absorption);
    }

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function show(Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        if ($buildingAbsorption->building_id !== $building->id) {
            return $this->error('Building Absorption not found for this Building', ['error_code' => 404]);
        }

        if ($buildingAbsorption->building_state !== BuildingState::ABSORPTION->value) {
            return $this->error('Invalid building state', ['error_code' => 403]);
        }

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }

        return $this->success(data: $buildingAbsorption);
    }


    /**
     * @param UpdateBuildingsAbsorptionRequest $request
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function update(UpdateBuildingsAbsorptionRequest $request, Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        if ($buildingAbsorption->building_id !== $building->id) {
            return $this->error('Building Absorption not found for this Building', ['error_code' => 404]);
        }
        if ($buildingAbsorption->building_state !== BuildingState::ABSORPTION->value) {
            return $this->error('Invalid building state', ['error_code' => 403]);
        }
        if ($buildingAbsorption->status == BuildingStatus::DRAFT->value) {
        return $this->error('Building must to have status Enabled', ['error_code' => 403]);
    }

        $validated = $request->validated();
        $validated['building_id'] = $building->id;
        $validated['building_state'] = BuildingState::ABSORPTION;
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
            $building = $this->buildingAvailableService->update($buildingAbsorption, $validated);

            if (!empty($building->fire_protection_system)) {
                $building->fire_protection_system = explode(',', $building->fire_protection_system);
            }
            if (!empty($building->above_market_tis)) {
                $building->above_market_tis = explode(',', $building->above_market_tis);
            }

            return $this->success('Building Absorption updated successfully', $buildingAbsorption);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function destroy(Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        if ($buildingAbsorption->building_id !== $building->id) {
            return $this->error('Building Absorption not found for this Building', ['error_code' => 404]);
        }

        if ($buildingAbsorption->building_state !== BuildingState::ABSORPTION->value) {
            return $this->error('Invalid building state', ['error_code' => 403]);
        }

        try {
            if ($buildingAbsorption->delete()) {
                return $this->success('Building Absorption deleted successfully', $buildingAbsorption);
            }
            return $this->error('Building Absorption delete failed', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @param ConvertToAvailableRequest $request
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function toAvailable(ConvertToAvailableRequest $request, Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        if ($buildingAbsorption->status === BuildingStatus::DRAFT->value) {
            return $this->error('Cannot convert from one draft.', status: 400);
        }
        $validated = $request->validated();
        if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
            $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
        }
        if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
            $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
        }
        $result = $this->buildingAvailableService->convertToAvailable($validated, $building->id, $buildingAbsorption->id);
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

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function draft(Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        $result = $this->buildingAvailableService->createDraft($building, $buildingAbsorption, BuildingState::ABSORPTION->value);

        if (isset($result['error'])) {
            return $this->error($result['error'], status: $result['status']);
        }

        return $this->success($result['success'], data: $result['data']);
    }

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function getDraft(Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        $result = $this->buildingAvailableService->getDraft( $building, $buildingAbsorption, BuildingState::ABSORPTION->value);

        if (isset($result['error'])) {
            return $this->error($result['error'], status: $result['status']);
        }

        return $this->success($result['success'], data: $result['data']);
    }

    /**
     * @param UpdateBuildingAbsorptionDraftRequest $request
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function updateDraft(UpdateBuildingAbsorptionDraftRequest $request, Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        $validated = $request->validated();
        $result = $this->buildingAvailableService->updateDraft($building, $buildingAbsorption, $validated, BuildingState::ABSORPTION->value);

        if (isset($result['error'])) {
            return $this->error($result['error'], status: $result['status']);
        }

        return $this->success($result['success'], data: $result['data']);

    }

    /**
     * @param Building $building
     * @param BuildingAvailable $buildingAbsorption
     * @return ApiResponse
     */
    public function deleteDraft(Building $building, BuildingAvailable $buildingAbsorption): ApiResponse
    {
        $result = $this->buildingAvailableService->deleteDraft($building, $buildingAbsorption, BuildingState::ABSORPTION->value);
        if (isset($result['error'])) {
            return $this->error($result['error'], status: $result['status']);
        }
        return $this->success($result['success'], data: $result['data']);
    }

}
