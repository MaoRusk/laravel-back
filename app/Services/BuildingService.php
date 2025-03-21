<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class BuildingService
{
    /**
     * Create a new class instance.
     */
    public function filter(array $validated): mixed
    {
        $size = $validated['size'] ?? 10;
        $order = $validated['column'] ?? 'id';
        $direction = $validated['state'] ?? 'desc';

        return Building::with(['market', 'subMarket', 'industrialPark'])
            ->when($validated['search'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search){
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('building_name', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? false, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($validated['building_name'] ??  false, function ($query, $building_name){
                $query->where('building_name', 'like', "%{$building_name}%");
            })
            ->when($validated['marketName'] ?? false, function ($query, $marketName) {
                $query->whereHas('market', function ($query) use ($marketName) {
                    $query->where('name', 'like', "%{$marketName}%");
                });
            })
            ->when($validated['submarketName'] ?? false, function ($query, $submarketName){
                $query->whereHas('subMarket', function ($query) use ($submarketName) {
                    $query->where('name', 'like', "%{$submarketName}%");
                });
            })
            ->when($validated['industrialParkName'] ?? false, function ($query, $industrialParkName) {
                $query->whereHas('industrialPark', function ($query) use ($industrialParkName) {
                    $query->where('name', 'like', "%{$industrialParkName}%");
                });
            })
            ->orderBy($order, $direction)
            ->paginate($size);
    }

    public function show(Building $building): Building
    {
        return $building->load([
            'region',
            'market',
            'subMarket',
            'builder',
            'industrialPark',
            'developer',
            'owner',
            'contact',
            'buildingsAvailable',
        ]);
    }

    public function create(array $validated): Building
    {
        return Building::create($validated);
    }

    public function update(Building $building, array $validated): Building
    {
        $building->update($validated);
        return $building;
    }

    public function convertMetrics(array $data): array
    {
        if (isset($data['building_size_sf'])) {
            $data['building_size_sf'] = $this->convertM2ToSqFt($data['building_size_sf']);
        }

        if (isset($data['total_land_sf'])) {
            $data['total_land_sf'] = $this->convertM2ToSqFt($data['total_land_sf']);
        }

        if (isset($data['clear_height_ft'])) {
            $data['clear_height_ft'] = $this->convertMToFt($data['clear_height_ft']);
        }

        if (isset($data['floor_thickness_in'])) {
            $data['floor_thickness_in'] = $this->convertInToCm($data['floor_thickness_in']);
        }

        if (isset($data['offices_space_sf'])) {
            $data['offices_space_sf'] = $this->convertM2ToSqFt($data['offices_space_sf']);
        }

        if (isset($data['columns_spacing_ft'])) {
            $data['columns_spacing_ft'] = $this->convertColumnsSpacingToFt($data['columns_spacing_ft']);
        }

        return $data;
    }

    public function convertM2ToSqFt(float $m2): int
    {
        return (int) round($m2 * 10.764);
    }

    public function convertMToFt(float $m): int
    {
        return (int) round($m * 3.281);
    }

    public function convertInToCm(float $in): int
    {
        return (int) round($in * 2.54);
    }

    public function convertColumnsSpacingToFt(string $spacing): string
    {
        $parts = explode('x', $spacing);
        $convertedParts = array_map(fn($value) => $this->convertMToFt((float) $value), $parts);
        return implode('x', $convertedParts);
    }

    public function getBuildingData($buildingId)
    {
        return Building::query()
            ->leftJoin('cat_markets as market', 'buildings.market_id', '=', 'market.id')
            ->leftJoin('cat_submarkets as submarket', 'buildings.submarket_id', '=', 'submarket.id')
            ->leftJoin('cat_industrial_parks as industrial_parks', 'buildings.industrial_park_id', '=', 'industrial_parks.id')
            ->leftJoin('buildings_available as building_av', 'building_av.building_id', '=', 'buildings.id')
            ->select([
                'buildings.id',
                'buildings.total_land_sf',
                'buildings.building_size_sf',
                'buildings.expansion_up_to_sf',
                'buildings.construction_type',
                'buildings.building_name',
                'buildings.floor_thickness_in',
                'buildings.floor_resistance',
                'buildings.roof_system',
                'buildings.clear_height_ft',
                'building_av.avl_building_dimensions_ft',
                'buildings.columns_spacing_ft',
                'buildings.bay_size',
                'building_av.dock_doors',
                'building_av.knockouts_docks',
                'building_av.truck_court_ft',
                'building_av.trailer_parking_space',
                'building_av.shared_truck',
                'buildings.offices_space_sf',
                'market.name as market_name',
                'submarket.name as submarket_name',
                'industrial_parks.name as industrial_park_name',
                'buildings.year_built',
                'buildings.currency',
            ])
            ->where('buildings.id', $buildingId)
            ->firstOrFail();
    }

    public function userData($userId)
    {
        return User::query()
            ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
            ->leftJoin('files', 'companies.logo_id', '=', 'files.id') // Unir con la tabla files
            ->select([
                'users.id',
                'users.name',
                'users.middle_name',
                'users.last_name',
                'users.email as email',
                'companies.name as company_name',
                'companies.primary_color',
                'companies.secondary_color',
                'files.path as logo_path' // Obtener la ruta del logo
            ])
            ->where('users.id', $userId)
            ->first();
    }

    public function getBuildingImages($buildingId)
    {
        // Obtener los archivos filtrando por los tipos necesarios
        $files = \DB::table('building_files')
            ->join('files', 'building_files.file_id', '=', 'files.id')
            ->where('building_files.building_id', $buildingId)
            ->whereIn('building_files.type', ['Front Page', 'Aerial', 'Gallery'])
            ->select('building_files.type', 'files.path')
            ->get();

        // Validar si los archivos existen en el directorio
        $validFiles = [];

        foreach ($files as $file) {
            $filePath = storage_path('app/public/' . $file->path); // Ruta del archivo en storage
            if (file_exists($filePath)) {
                $validFiles[] = [
                    'type' => $file->type,
                    'url' => asset('storage/' . $file->path) // Convertir a URL accesible
                ];
            }
        }

        return $validFiles;
    }

    public function layoutDesign($buildingId)
    {
        $userId = auth()->id();
        $user = $this->userData($userId);
        $building = $this->getBuildingData($buildingId);
        $images = $this->getBuildingImages($buildingId); // Obtener imágenes validadas

        // Obtener la ruta del logo
        $logoPath = $user->logo_path ? storage_path('app/public/' . $user->logo_path) : null;
        $pdf = Pdf::loadView('buildings.layout-design', compact('building', 'user', 'logoPath', 'images'));
        return $pdf->stream('layout-design.pdf');
    }
}
