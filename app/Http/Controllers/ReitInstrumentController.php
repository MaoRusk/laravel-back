<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReitInstrumentRequest;
use App\Http\Requests\UpdateReitInstrumentRequest;
use App\Models\ReitInstrument;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReitInstrumentController extends ApiController implements HasMiddleware
{
    //
    public static function middleware()
    {
        return [
            new Middleware('permission:reit-instruments.show', only: ['show']),
            new Middleware('permission:reit-instruments.create', only: ['store']),
            new Middleware('permission:reit-instruments.update', only: ['update']),
            new Middleware('permission:reit-instruments.destroy', only: ['destroy']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $reitInstruments = ReitInstrument::all();
        return $this->success(data: $reitInstruments);
    }

    /**
     * @param StoreReitInstrumentRequest $request
     * @return ApiResponse
     */
    public function store(StoreReitInstrumentRequest $request): ApiResponse
    {
        $reitInstrument = ReitInstrument::create($request->validated());
        return $this->success('Reit Instrument created successfully', $reitInstrument);
    }

    /**
     * @param ReitInstrument $reitInstrument
     * @return ApiResponse
     */
    public function show(ReitInstrument $reitInstrument): ApiResponse
    {
        $reitInstrument->load(['reitType']);
        return $this->success(data: $reitInstrument);
    }

    /**
     * @param UpdateReitInstrumentRequest $request
     * @param ReitInstrument $reitInstrument
     * @return ApiResponse
     */
    public function update(UpdateReitInstrumentRequest $request, ReitInstrument $reitInstrument): ApiResponse
    {
        try {
            if($reitInstrument->update($request->validated())) {
                return $this->success('Reit Instrument updated successfully', $reitInstrument);
            }
            return $this->error('Reit Instrument updated field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param ReitInstrument $reitInstrument
     * @return ApiResponse
     */
    public function destroy(ReitInstrument $reitInstrument): ApiResponse
    {
        try {
            if($reitInstrument->delete()) {
                return $this->success('Reit Instrument deleted successfully', $reitInstrument);
            }
            return $this->error('Reit Instrument deleted field', status: 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
