<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLandsAvailableRequest;
use App\Http\Requests\StoreLandAvailableRequest;
use App\Http\Requests\UpdateLandAvailableRequest;
use App\Models\Land;
use App\Models\LandAvailable;
use App\Services\LandsAvailableService;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandAvailableController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:lands.available.show', only: ['show']),
            new Middleware('permission:lands.available.create', only: ['store']),
            new Middleware('permission:lands.available.update', only: ['update']),
            new Middleware('permission:lands.available.destroy', only: ['destroy']),
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
    public function index(IndexLandsAvailableRequest $request, Land $land): ApiResponse
    {
        $validated = $request->validated();

        $landsAvailable = $this->landsAvailableService->filterAvailable($validated, $land->id);
        return $this->success(data: $landsAvailable);
    }


    /**
     * @param Land $land
     * @param LandAvailable $landAvailable
     * @return ApiResponse
     */
    public function show(Land $land, LandAvailable $landAvailable): ApiResponse
    {
        if ($landAvailable->land_id !== $land->id) {
            return $this->error('Land Available not found for this Land', ['error_code' => 404]);
        }

        if ($landAvailable->state !== 'Availability') {
            return $this->error('Invalid land state', ['error_code' => 403]);
        }

        return $this->success(data: $landAvailable);
    }


    /**
     * @param StoreLandAvailableRequest $request
     * @param Land $land
     * @return ApiResponse
     */
    public function store(StoreLandAvailableRequest $request, Land $land): ApiResponse
    {
        $validatedData = $request->validated();

        $validatedData['land_id'] = $land->id;
        $validatedData['state'] = 'Availability';

        $landExists = Land::where('id', $land->id)->exists();
        if (!$landExists) {
            return $this->error('Invalid land_id: The land does not exist.', 422);
        }

        $landAvailable = LandAvailable::create($validatedData);

        return $this->success('Land Available created successfully', $landAvailable);
    }


    /**
     * @param UpdateLandAvailableRequest $request
     * @param Land $land
     * @param LandAvailable $landAvailable
     * @return ApiResponse
     */
    public function update(UpdateLandAvailableRequest $request, Land $land, LandAvailable $landAvailable): ApiResponse
    {
        try {
            if ($landAvailable->land_id !== $land->id) {
                return $this->error('The provided land does not match the available record.', 422);
            }

            $validatedData = $request->validated();

            $validatedData['land_id'] = $land->id;
            $validatedData['state'] = 'Availability';

            $landAvailable->update($validatedData);

            return $this->success('Land Available updated successfully', $landAvailable);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    /**
     * @param Land $land
     * @param LandAvailable $landAvailable
     * @return ApiResponse
     */
    public function destroy(Land $land, LandAvailable $landAvailable): ApiResponse
    {
        if ($landAvailable->land_id !== $land->id) {
            return $this->error('Land Available not found for this Land', ['error_code' => 404]);
        }

        if ($landAvailable->state !== 'Availability') {
            return $this->error('Invalid land state', ['error_code' => 403]);
        }

        try {
            if ($landAvailable->delete()) {
                return $this->success('Land Available deleted successfully', $landAvailable);
            }
            return $this->error('Land Available delete failed', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
