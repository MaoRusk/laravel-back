<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Models\ReitType;
use App\Http\Requests\StoreReitTypeRequest;
use App\Http\Requests\UpdateReitTypeRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReitTypeController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:reit-types.show', only: ['show']),
            new Middleware('permission:reit-types.create', only: ['store']),
            new Middleware('permission:reit-types.update', only: ['update']),
            new Middleware('permission:reit-types.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $reitTypes = ReitType::all();
        return $this->success(data: $reitTypes);
    }

    /**
     * @param StoreReitTypeRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitTypeRequest $request): ApiResponse
    {
        $reitType = ReitType::create($request->validated());
        return $this->success('Reit type created successfully', $reitType);
    }

    /**
     * @param ReitType $reitType
     * @return ApiResponse
     */
    public function show(ReitType $reitType): ApiResponse
    {
        return $this->success(data: $reitType);
    }

    /**
     * @param UpdateReitTypeRequest $request
     * @param ReitType $reitType
     * @return ApiResponse
     */
    public function update(UpdateReitTypeRequest $request, ReitType $reitType): ApiResponse
    {
        try{
            if($reitType->update($request->validated())) {
                return $this->success('Reit type updated successfully', $reitType);
            }
            return  $this->error('Reit type updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param ReitType $reitType
     * @return ApiResponse
     */
    public function destroy(ReitType $reitType): ApiResponse
    {
        try{
            if($reitType->delete()) {
                return $this->success('Reit type deleted successfully', $reitType);
            }
            return $this->error('Reit type deleted filed', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
