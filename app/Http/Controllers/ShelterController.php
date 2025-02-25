<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ShelterController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:shelters.index', only: ['index']),
        ];
    }

    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $shelters = Shelter::all();
        return $this->success(data: $shelters);
    }

}
