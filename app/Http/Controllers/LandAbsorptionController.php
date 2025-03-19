<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLandsAbsorptionRequest;
use App\Http\Requests\StoreLandAbsorptionRequest;
use App\Http\Requests\UpdateLandAbsorptionRequest;
use App\Models\Land;
use App\Models\LandAvailable;
use App\Services\LandsAvailableService;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandAbsorptionController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:lands.absorption.show', only: ['show']),
            new Middleware('permission:lands.absorption.create', only: ['store']),
            new Middleware('permission:lands.absorption.update', only: ['update']),
            new Middleware('permission:lands.absorption.destroy', only: ['destroy']),
        ];
    }

    private LandsAvailableService $landsAvailableService;

    public function __construct(LandsAvailableService $landsAvailableService)
    {
        $this->landsAvailableService = $landsAvailableService;
    }

    /**
     * @param Land $land
     * @return ApiResponse
     */
    public function index(IndexLandsAbsorptionRequest $request, Land $land): ApiResponse
    {
        $validated = $request->validated();
        $landsAbsorption = $this->landsAvailableService->filterAbsorption($validated, $land->id);
        return $this->success(data: $landsAbsorption);
    }

    /**
     * @param Land $land
     * @param LandAvailable $landAbsorption
     * @return ApiResponse
     */
    public function show(Land $land, LandAvailable $landAbsorption): ApiResponse
    {
        if ($landAbsorption->land_id !== $land->id) {
            return $this->error('Land Absorption not found for this Land', ['error_code' => 404]);
        }

        if ($landAbsorption->state !== 'Absorption') {
            return $this->error('Invalid land state', ['error_code' => 403]);
        }

        return $this->success(data: $landAbsorption);
    }

    /**
     * @param StoreLandAbsorptionRequest $request
     * @param Land $land
     * @return ApiResponse
     */
    public function store(StoreLandAbsorptionRequest $request, Land $land): ApiResponse
    {
        $validatedData = $request->validated();

        $validatedData['land_id'] = $land->id;
        $validatedData['state'] = 'Absorption';

        $landExists = Land::where('id', $land->id)->exists();
        if (!$landExists) {
            return $this->error('Invalid land_id: The land does not exist.', 422);
        }

        $landAbsorption = LandAvailable::create($validatedData);

        return $this->success('Land Absorption created successfully', $landAbsorption);
    }

    /**
     * @param UpdateLandAbsorptionRequest $request
     * @param Land $land
     * @param LandAvailable $landAbsorption
     * @return ApiResponse
     */
    public function update(UpdateLandAbsorptionRequest $request, Land $land, LandAvailable $landAbsorption): ApiResponse
    {
        try {
            if ($landAbsorption->land_id !== $land->id) {
                return $this->error('The provided land does not match the absorption record.', 422);
            }

            $validatedData = $request->validated();

            $validatedData['land_id'] = $land->id;
            $validatedData['state'] = 'Absorption';

            $landAbsorption->update($validatedData);

            return $this->success('Land Absorption updated successfully', $landAbsorption);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @param Land $land
     * @param LandAvailable $landAbsorption
     * @return ApiResponse
     */
    public function destroy(Land $land, LandAvailable $landAbsorption): ApiResponse
    {
        if ($landAbsorption->land_id !== $land->id) {
            return $this->error('Land Absorption not found for this Land', ['error_code' => 404]);
        }

        if ($landAbsorption->state !== 'Absorption') {
            return $this->error('Invalid land state', ['error_code' => 403]);
        }

        try {
            if ($landAbsorption->delete()) {
                return $this->success('Land Absorption deleted successfully', $landAbsorption);
            }
            return $this->error('Land Absorption delete failed', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

}
