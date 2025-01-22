<?php

namespace App\Services;

use App\Models\BuildingAvailable;

class BuildingsAvailableService
{
    /**
     * Create a new class instance.
     */
    public function filterAvailable(array $validatedData, int $buildingId)
    {
        $size = $validatedData['size'] ?? 10;
        $order = $validatedData['column'] ?? 'id';
        $direction = $validatedData['state'] ?? 'desc';

        return BuildingAvailable::where('building_id', $buildingId)
            ->where('building_state', '=', 'Availability')
            ->when($validatedData['search'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('building_state', 'like', "%{$search}%")
                        ->orWhere('avl_size_sf', 'like', "%{$search}%")
                        ->orWhere('dock_doors', 'like', "%{$search}%");
                });
            })
            ->when($validatedData['avl_size_sf'] ?? false, function ($query, $avl_size_sf) {
                $query->where('avl_size_sf', 'like', "%{$avl_size_sf}%");
            })
            ->when($validatedData['avl_building_dimensions'] ?? false, function ($query, $avl_building_dimensions) {
                $query->where('avl_building_dimensions', 'like', "%{$avl_building_dimensions}%");
            })
            ->when($validatedData['avl_minimum_space_sf'] ?? false, function ($query, $avl_minimum_space_sf) {
                $query->where('avl_minimum_space_sf', 'like', "%{$avl_minimum_space_sf}%");
            })
            ->when($validatedData['avl_expansion_up_to_sf'] ?? false, function ($query, $avl_expansion_up_to_sf) {
                $query->where('avl_expansion_up_to_sf', 'like', "%{$avl_expansion_up_to_sf}%");
            })
            ->when($validatedData['dock_doors'] ?? false, function ($query, $dock_doors) {
                $query->where('dock_doors', 'like', "%{$dock_doors}%");
            })
            ->orderBy($order, $direction)
            ->paginate($size);
    }

    /**
     * @param array $validatedData
     * @param int $buildingId
     * @param int $buildingAvailableId
     * @return array|\Illuminate\Database\Eloquent\TModel
     */
    public function convertToAbsorption(array $validatedData, int $buildingId, int $buildingAvailableId) {
        $buildingAvailable = BuildingAvailable::where('building_id', $buildingId)
            ->where('id', $buildingAvailableId)
            ->firstOrFail();

        if ($buildingAvailable->building_state !== 'Availability') {
            return [
                'success' => false,
                'message' => 'Only records with state "Available" can be converted to "Absorption".',
                'code' => 400
            ];
        }

        $validatedData['building_state'] = 'Absorption';

        $buildingAvailable->update($validatedData);

        return [
            'success' => true,
            'data' => $buildingAvailable
        ];
    }
}
