<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReitRequest;
use App\Http\Requests\UpdateReitRequest;
use App\Models\Reit;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReitController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:reits.show', only: ['show']),
            new Middleware('permission:reits.create', only: ['store']),
            new Middleware('permission:reits.update', only: ['update']),
            new Middleware('permission:reits.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $reits = Reit::all();
        return $this->success(data: $reits);
    }

    /**
     * @param StoreReitRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitRequest $request): ApiResponse
    {
        $reit = Reit::create($request->validated());
        return $this->success('Reit created successfully', $reit);
    }

    /**
     * @param Reit $reit
     * @return ApiResponse
     */
    public function show(Reit $reit): ApiResponse
    {
        return $this->success(data: $reit);
    }

    /**
     * @param UpdateReitRequest $request
     * @param Reit $reit
     * @return ApiResponse
     */
    public function update(UpdateReitRequest $request, Reit $reit): ApiResponse
    {
        try{
            if($reit->update($request->validated())) {
                return $this->success('Reit updated successfully', $reit);
            }
            return  $this->error('Reit updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param Reit $reit
     * @return ApiResponse
     */
    public function destroy(Reit $reit): ApiResponse
    {
        try{
            if($reit->delete()) {
                return $this->success('Reit deleted successfully', $reit);
            }
            return $this->error('Reit deleted filed', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
