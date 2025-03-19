<?php

namespace App\Http\Controllers;

use App\Enums\BuildingClass;
use App\Enums\BuildingDeal;
use App\Enums\BuildingFireProtectionSystem;
use App\Enums\BuildingLightning;
use App\Enums\BuildingLoadingDoor;
use App\Enums\BuildingPhase;
use App\Enums\BuildingTenancy;
use App\Enums\BuildingTypeConstruction;
use App\Enums\BuildingTypeGeneration;
use App\Enums\TechnicalImprovements;
use App\Enums\BuildingStatus;
use App\Enums\BuildingCompanyType;
use App\Enums\BuildingFinalUse;
use App\Http\Requests\IndexBuildingRequest;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingDraftRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Models\Building;
use App\Services\BuildingService;
use App\Responses\ApiResponse;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PDF;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BuildingController extends ApiController implements HasMiddleware
{
    private BuildingService $buildingService;
    private FileService $fileService;

    public static function middleware()
    {
        return [
            new Middleware('permission:buildings.show', only: ['show']),
            new Middleware('permission:buildings.create', only: ['store']),
            new Middleware('permission:buildings.update', only: ['update']),
            new Middleware('permission:buildings.approve', only: ['approve']),
            new Middleware('permission:buildings.draft', only: ['draft']),
            new Middleware('permission:buildings.uploadFiles', only: ['uploadFiles']),
            new Middleware('permission:buildings.layoutDesign', only: ['layoutDesign']),
        ];
    }

    public function __construct(BuildingService $buildingService, FileService $fileService)
    {
        $this->buildingService = $buildingService;
        $this->fileService = $fileService;
    }

    public function index(IndexBuildingRequest $request): ApiResponse
    {
        $buildings = $this->buildingService->filter($request->validated());
        return $this->success(data: $buildings);
    }

    public function store(StoreBuildingRequest $request): ApiResponse
    {
        $validated = $request->validated();

        if ($validated['sqftToM2'] ?? false) {
            $validated = $this->buildingService->convertMetrics($validated);
        }
        if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
            $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
        }
        if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
            $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
        }

        $building = $this->buildingService->create($validated);

        if ($request->hasFile('files')) {
            $type = $request->input('type');
            $uploadedFilesInfo = $this->fileService->uploadBuildingFiles($request->file('files'), $building->id, $type);
        }

        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }
        return $this->success('Building created successfully', [
            'building' => $building,
            'uploaded_files' => $uploadedFilesInfo ?? null,
        ]);
    }

    public function show(Building $building): ApiResponse
    {
        $building = $this->buildingService->show($building);
        if (!empty($building->fire_protection_system)) {
            $building->fire_protection_system = explode(',', $building->fire_protection_system);
        }
        if (!empty($building->above_market_tis)) {
            $building->above_market_tis = explode(',', $building->above_market_tis);
        }
        return $this->success(data: $building);
    }

    /**
     * @param UpdateBuildingRequest $request
     * @param Building $building
     * @return ApiResponse
     */
    public function update(UpdateBuildingRequest $request, Building $building): ApiResponse
    {
        try {
            $validated = $request->validated();

            if ($validated['sqftToM2'] ?? false) {
                $validated = $this->buildingService->convertMetrics($validated);
            }

            if (!empty($validated['fire_protection_system']) && is_array($validated['fire_protection_system'])) {
                $validated['fire_protection_system'] = implode(',', $validated['fire_protection_system']);
            }
            if (!empty($validated['above_market_tis']) && is_array($validated['above_market_tis'])) {
                $validated['above_market_tis'] = implode(',', $validated['above_market_tis']);
            }

            $building = $this->buildingService->update($building, $validated);

            if ($request->hasFile('files')) {
                $type = $request->input('type');

                $deletedFiles = $this->fileService->deleteBuildingFilesByType($request->file('files'), $building->id);

                $uploadedFilesInfo = $this->fileService->uploadBuildingFiles($request->file('files'), $building->id, $type);
            }

            if (!empty($building->fire_protection_system)) {
                $building->fire_protection_system = explode(',', $building->fire_protection_system);
            }
            if (!empty($building->above_market_tis)) {
                $building->above_market_tis = explode(',', $building->above_market_tis);
            }
            return $this->success('Building updated successfully', [
                'building' => $building,
                'uploaded_files' => $uploadedFilesInfo ?? null,
                'deleted_files' => $deletedFiles ?? null,
            ]);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }

    /**
     * @param Building $building
     * @return ApiResponse
     */
    public function destroy(Building $building): ApiResponse
    {
        try {
            if ($building->delete()) {
                return $this->success('Building deleted successfully', $building);
            }
            // return $this->error('Building delete failed', ['error_code' => 422]);
            return $this->error('Building delete failed', status:422);
        } catch (\Exception $e) {
            // return $this->error($e->getMessage(), ['error_code' => 500]);
            return $this->error($e->getMessage(), status:500);
        }
    }

    /**
     * @return ApiResponse
     */
    public function listClasses(): ApiResponse
    {

        return $this->success(data: BuildingClass::array());
    }

    /**
     * @return ApiResponse
     */
    public function listLoadingDoors(): ApiResponse
    {
        return $this->success(data: BuildingLoadingDoor::array());
    }

    /**
     * @return ApiResponse
     */
    public function listPhases(): ApiResponse
    {
        $phases = BuildingPhase::array();

        $filteredPhases = collect($phases)
            ->when(request()->boolean('availability'), function ($collection) {
                return $collection->filter(fn($phase) => in_array($phase, [
                    BuildingPhase::CONSTRUCTION->value,
                    BuildingPhase::PLANNED->value,
                BuildingPhase::SUBLEASE->value,
                BuildingPhase::EXPIRATION->value,
            ]));
        })
            ->when(request()->boolean('absorption'), function ($collection) {
                return $collection->filter(fn($phase) => in_array($phase, [
                    BuildingPhase::BTS->value,
                    BuildingPhase::EXPANSION->value,
            ]));
        });

        return $this->success(data: $filteredPhases->values());
    }

    /**
     * @return ApiResponse
     */
    public function listLightnings(): ApiResponse
    {
        return $this->success(data: BuildingLightning::array());
    }


    /**
     * @return ApiResponse
     */
    public function listFireProtectionSystems(): ApiResponse
    {
        return $this->success(data: BuildingFireProtectionSystem::array());
    }

    /**
     * @return ApiResponse
     */
    public function listTenancies(): ApiResponse
    {
        return $this->success(data: BuildingTenancy::array());
    }

    /**
     * @return ApiResponse
     */
    public function listTypeConstructions(): ApiResponse
    {
        return $this->success(data: BuildingTypeConstruction::array());
    }

    /**
     * @return ApiResponse
     */
    public function listTypeGenerations(): ApiResponse
    {
        return $this->success(data: BuildingTypeGeneration::array());
    }

    /**
     * @return ApiResponse
     */
    public function listDeals(): ApiResponse
    {
        return $this->success(data: BuildingDeal::array());
    }

    /**
     * @return ApiResponse
     */
    public function listTechnicalImprovements(): ApiResponse
    {
        return $this->success(data: TechnicalImprovements::array());
    }

    /**
     * @return ApiResponse
     */
    public function listBuildingsStatus(): ApiResponse
    {
        return $this->success(data: BuildingStatus::array());
    }

    /**
     * @return ApiResponse
     */
    public function listBuildingsCompanyTypes(): ApiResponse
    {
        return $this->success(data: BuildingCompanyType::array());
    }

    /**
     * @return ApiResponse
     */
    public function listFinalUses(): ApiResponse
    {
        return $this->success(data: BuildingFinalUse::array());
    }

    public function layoutDesign($buildingId)
    {
        return response()->make(
            $this->buildingService->layoutDesign($buildingId),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * @param Building $building
     * @return ApiResponse
     */
    public function draft(Building $building): ApiResponse
    {
        $result = $this->buildingService->createDraft($building);

        if (isset($result['error'])) {
            return $this->error($result['error'], status: $result['status']);
        }

        return $this->success($result['success'], data: $result['data']);
    }

    /**
     * @param Building $building
     * @return ApiResponse
     */
    public function getDraft(Building $building): ApiResponse
    {
        $draft = $this->buildingService->getDraft($building);

        if (!$draft) {
            return $this->error(message: 'Draft not found.', status: 404);
        }

        return $this->success(data: $draft);
    }

    /**
     * @param UpdateBuildingDraftRequest $request
     * @param Building $building
     * @return ApiResponse
     */
    public function updateDraft(UpdateBuildingDraftRequest $request, Building $building): ApiResponse
    {

        $validated = $request->validated();
        $result = $this->buildingService->updateDraft($building, $validated);

        if (!$result) {
            return $this->error('Draft not found.', status: 404);
        }

        $message = ($result->status === BuildingStatus::DRAFT->value)
            ? 'Draft updated successfully.'
            : 'Draft deleted and applied to the original building.';

        return $this->success(message: $message, data: $result);
    }

    /**
     * @param Building $building
     * @return ApiResponse
     */
    public function deleteDraft(Building $building): ApiResponse
    {
        $result = $this->buildingService->deleteDraft($building);
        if(isset($result['error'])) {
            return $this->error($result['error'], status: $result['status']);
        }
        return $this->success($result['success'], data: $result['data']);
    }

}
