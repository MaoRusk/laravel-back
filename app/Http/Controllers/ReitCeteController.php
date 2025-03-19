<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReitCeteRequest;
use App\Http\Requests\UpdateReitCeteRequest;
use App\Models\ReitCete;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReitCeteController extends ApiController implements HasMiddleware
{
    //
    public static function middleware()
    {
        return [
            new Middleware('permission:reit-cetes.show', only: ['show']),
            new Middleware('permission:reit-cetes.create', only: ['store']),
            new Middleware('permission:reit-cetes.update', only: ['update']),
            new Middleware('permission:reit-cetes.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $reitsCetes = ReitCete::all();
        return $this->success(data: $reitsCetes);
    }

    /**
     * @param StoreReitCeteRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitCeteRequest $request): ApiResponse
    {
        $reitCete = ReitCete::create($request->validated());
        return $this->success('Reit Cetes created successfully', $reitCete);
    }

    /**
     * @param ReitCete $reitCete
     * @return ApiResponse
     */
    public function show(ReitCete $reitCete): ApiResponse
    {
        $reitCete->load([
            'reit',
            'reitType',
        ]);
        return $this->success(data: $reitCete);
    }

    /**
     * @param UpdateReitCeteRequest $request
     * @param ReitCete $reitCete
     * @return ApiResponse
     */
    public function update(UpdateReitCeteRequest $request, ReitCete $reitCete): ApiResponse
    {
        try {
            if($reitCete->update($request->validated())) {
                return $this->success('Reit Cetes updated successfully', $reitCete);
            }
            return $this->error('Reit Cetes updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param ReitCete $reitCete
     * @return ApiResponse
     */
    public function destroy(ReitCete $reitCete): ApiResponse
    {
        try {
            if($reitCete->delete()) {
                return $this->success('Reit Cete deleted successfully', $reitCete);
            }
            return $this->error('Reit Cete deleted field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
