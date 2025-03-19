<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Models\Country;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CountryController extends ApiController
{


    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $countries = Country::all();
        return $this->success(data: $countries);
    }
}
