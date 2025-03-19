<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MarketController extends ApiController
{


    public function index(Request $request): ApiResponse
    {
        $query = Market::query();

        if ($request->has('region_id')) {
            $query->where('region_id', $request->input('region_id'));
        }
        $query->where('status', '=', 'active');
        $markets = $query->get();

        return $this->success(data: $markets);
    }
}
