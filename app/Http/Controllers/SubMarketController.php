<?php

namespace App\Http\Controllers;

use App\Models\SubMarket;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubMarketController extends ApiController
{


    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function index(Request $request): ApiResponse
    {
        $marketId = $request->query('market_id');

        $query = SubMarket::where('status', '=', 'active');

        if ($marketId) {
            $query->where('market_id', '=', $marketId);
        }

        $subMarkets = $query->get();

        return $this->success(data: $subMarkets);
    }
}
