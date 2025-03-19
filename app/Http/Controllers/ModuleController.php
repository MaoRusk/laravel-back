<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ModuleController extends ApiController
{


    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $modules = Module::all();
        return $this->success(data: $modules);
    }

}
