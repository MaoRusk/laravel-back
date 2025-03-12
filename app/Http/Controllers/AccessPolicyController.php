<?php

namespace App\Http\Controllers;

use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AccessPolicy;
use App\Http\Resources\AccessPolicyResource;
use App\Http\Requests\StoreAccessPolicyRequest;
use App\Http\Requests\UpdateAccessPolicyRequest;

class AccessPolicyController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ApiResponse
    {
        $policies = AccessPolicy::all();
        return $this->success(data: $policies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccessPolicyRequest $request): ApiResponse
    {
        $policy = AccessPolicy::create($request->validated());
        return $this->success('Access Policy created successfully', $policy);

    }

    /**
     * Display the specified resource.
     */
    public function show($id): ApiResponse
    {
        $policy = AccessPolicy::findOrFail($id);
        return $this->success(data: $policy);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccessPolicyRequest $request, $id): ApiResponse
    {
        $policy = AccessPolicy::findOrFail($id);
        $policy->update($request->validated());

        try {
            if ($policy->update($request->validated())) {
                return $this->success('Access Policy updated successfully', $policy);
            }

            return $this->error('Access Policy update failed', status:422);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status:500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): ApiResponse
    {
        $policy = AccessPolicy::findOrFail($id);

        try{

            if($policy) {
                $policy->delete();
                return $this->success('Access Policy deleted successfully', $policy);
            }

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
