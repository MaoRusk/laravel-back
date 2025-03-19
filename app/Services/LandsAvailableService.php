<?php


namespace App\Services;


use App\Models\LandAvailable;

class LandsAvailableService
{
    /**
     * @param array $validatedData
     * @param int $landId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filterAbsorption(array $validatedData, int $landId)
    {
        $size = $validatedData['size'] ?? 10;
        $order = $validatedData['column'] ?? 'id';
        $direction = $validatedData['state'] ?? 'desc';

        return LandAvailable::with('absIndustry', 'absBroker')
            ->where('land_id', $landId)
            ->where('state', '=', 'Absorption')
            ->when($validatedData['search'] ?? false, function ($query, $search) {
        $query->where(function ($query) use ($search) {
            $query->where('state', 'like', "%{$search}%")
                ->orWhere('land_condition', 'like', "%{$search}%")
                ->orWhere('abs_size_ha', 'like', "%{$search}%")
                ->orWhere('abs_broker_id', 'like', "%{$search}%")
                ->orWhere('abs_industry_id', 'like', "%{$search}%");
        });
    })
        ->when($validatedData['land_condition'] ?? false, function ($query, $land_condition) {
            $query->where('land_condition', 'like', "%{$land_condition}%");
        })
        ->when($validatedData['abs_size_ha'] ?? false, function ($query, $abs_size_ha) {
            $query->where('abs_size_ha', 'like', "%{$abs_size_ha}%");
        })
        ->when($validatedData['abs_broker_id'] ?? false, function ($query, $abs_broker_id) {
            $query->where('abs_broker_id', 'like', "%{$abs_broker_id}%");
        })
        ->when($validatedData['abs_industry_id'] ?? false, function ($query, $abs_industry_id) {
            $query->where('abs_industry_id', 'like', "%{$abs_industry_id}%");
        })
        ->orderBy($order, $direction)
        ->paginate($size);
    }

    /**
     * @param array $validatedData
     * @param int $landId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filterAvailable(array $validatedData, int $landId)
    {
        $size = $validatedData['size'] ?? 10;
        $order = $validatedData['column'] ?? 'id';
        $direction = $validatedData['state'] ?? 'desc';

        return LandAvailable::with('avlBroker')
            ->where('land_id', $landId)
            ->where('state', '=', 'Availability')
            ->when($validatedData['search'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('state', 'like', "%{$search}%")
                        ->orWhere('land_condition', 'like', "%{$search}%")
                        ->orWhere('avl_size_ha', 'like', "%{$search}%")
                        ->orWhere('avl_broker_id', 'like', "%{$search}%")
                        ->orWhere('avl_deal', 'like', "%{$search}%")
                        ->orWhere('avl_minimum_size_ha', 'like', "%{$search}%");
                });
            })
            ->when($validatedData['land_condition'] ?? false, function ($query, $land_condition) {
                $query->where('land_condition', 'like', "%{$land_condition}%");
            })
            ->when($validatedData['avl_size_ha'] ?? false, function ($query, $avl_size_ha) {
                $query->where('avl_size_ha', 'like', "%{$avl_size_ha}%");
            })
            ->when($validatedData['avl_broker_id'] ?? false, function ($query, $avl_broker_id) {
                $query->where('avl_broker_id', 'like', "%{$avl_broker_id}%");
            })
            ->when($validatedData['avl_deal'] ?? false, function ($query, $avl_deal) {
                $query->where('avl_deal', 'like', "%{$avl_deal}%");
            })
            ->when($validatedData['avl_minimum_size_ha'] ?? false, function ($query, $avl_minimum) {
                $query->where('avl_minimum_size_ha', 'like', "%{$avl_minimum}%");
            })
            ->orderBy($order, $direction)
            ->paginate($size);
    }
}
