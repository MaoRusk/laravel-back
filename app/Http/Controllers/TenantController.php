<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Models\Tenant;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TenantController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:tenants.show', only: ['show']),
            new Middleware('permission:tenants.create', only: ['store']),
            new Middleware('permission:tenants.update', only: ['update']),
            new Middleware('permission:tenants.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $tenants = Tenant::all();
        return $this->success(data: $tenants);
    }


    /**
     * @param StoreTenantRequest $request
     * @return ApiResponse
     */
    public function store(StoreTenantRequest $request): ApiResponse
    {
        $tenant = Tenant::create($request->validated());
        return $this->success('Tenant created successfully', $tenant);
    }

    /**
     * @param Tenant $tenant
     * @return ApiResponse
     */
    public function show(Tenant $tenant): \App\Responses\ApiResponse
    {
        return $this->success(data: $tenant);
    }

    /**
     * @param UpdateTenantRequest $request
     * @param Tenant $tenant
     * @return ApiResponse
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): ApiResponse
    {
        try {
            if ($tenant->update($request->validated())) {
                return $this->success('Tenant updated successfully', $tenant);
            }

            return $this->error('Tenant update failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }


    /**
     * @param Tenant $tenant
     * @return ApiResponse
     */
    public function destroy(Tenant $tenant): ApiResponse
    {
        try {
            if ($tenant->delete()) {
                return $this->success('Tenant deleted successfully', $tenant);
            }
            return $this->error('Tenant delete failed', status:422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }
    }

}
