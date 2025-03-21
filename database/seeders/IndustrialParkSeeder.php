<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\IndustrialPark;
use App\Models\Market;
use App\Models\Region;
use App\Models\SubMarket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class IndustrialParkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /*public function run(): void
    {
        $path = storage_path('app/industrial-parks-1.csv');
        $file = fopen($path, 'r');

        fgetcsv($file);

        $data = [];
        $id = 1;

        while (($row = fgetcsv($file)) !== false) {
            $market = Market::where('name', $row[8])->first();
            if(!$market) {
                $region = Region::where('name', $row[7])->first();
                if($region) {
                    $market = Market::create([
                        'name' => $row[8],
                        'region_id' => $region->id,
                        'status' => 'active',
                    ]);
                }
            }

            $submarket = SubMarket::where('name', $row[9])->first();
            if(!$submarket) {
                $market = Market::where('name', $row[8])->first();
                if($market) {
                    $submarket = SubMarket::create([
                        'name' => $row[9],
                        'market_id' => $market->id,
                        'status' => 'active',
                    ]);
                }
            }

            if($market && $submarket) {
                $owner = Developer::where('name', $row[6])->first();
                $data[] = [
                    'name' => $row[0],
                    'market_id' => $market->id,
                    'sub_market_id' => $submarket->id,
                    'owner_id' => $owner?->id,
                    'region_id' => $region?->id ?? null,
                    'total_land_ha' => $row[2] ?? null,
                    'available_land_ha' =>$row[3] ?? null,
                    'reserve_land_ha' => $row[4] ?? null,
                    'building_number' => $row[5] ?? null,
                    'land_condition' => $row[10] ?? null,
                    'park_type' => 'Industrial Park',
                    'year_built' => $row[11] ?? null,
                    'has_rail_spur' => ($row[12] === 'Yes') ? 1 : 0,
                    'has_natural_gas' => ($row[13] === 'Yes') ? 1 : 0,
                    'has_sewage' => ($row[14] === 'Yes') ? 1 : 0,
                    'has_water' => ($row[15] === 'Yes') ? 1 : 0,
                    'has_electric' => ($row[16] === 'Yes') ? 1 : 0,
                    'latitude' => $row[18] ?? null,
                    'longitude' => $row[19] ?? null,
                    'comments' => $row[17] ?? null,
                ];
            }

        }

        fclose($file);

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('industrial_parks')->insertOrIgnore($chunk);
        }

    }*/
    public function run(): void
    {
        $path = storage_path('app/industrial-parks-1.csv');
        $file = fopen($path, 'r');

        fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 20) continue;

            $market = Market::firstOrCreate(
                ['name' => $row[8]],
                ['region_id' => Region::where('name', $row[7])->first()?->id ?? null, 'status' => 'active']
            );

            $submarket = SubMarket::firstOrCreate(
                ['name' => $row[9]],
                ['market_id' => $market->id, 'status' => 'active']
            );

            $owner = Developer::where('name', $row[6])->first();

            $data[] = [
                'name' => $row[0],
                'market_id' => $market->id,
                'sub_market_id' => $submarket->id,
                'owner_id' => $owner?->id,
                'region_id' => $market->region_id,
                'total_land_ha' => $row[2] ?? null,
                'available_land_ha' => $row[3] ?? null,
                'reserve_land_ha' => $row[4] ?? null,
                'building_number' => $row[5] ?? null,
                'land_condition' => $row[10] ?? null,
                'park_type' => 'Pocket',
                'year_built' => $row[11] ?? null,
                'has_rail_spur' => ($row[12] === 'Yes') ? 1 : 0,
                'has_natural_gas' => ($row[13] === 'Yes') ? 1 : 0,
                'has_sewage' => ($row[14] === 'Yes') ? 1 : 0,
                'has_water' => ($row[15] === 'Yes') ? 1 : 0,
                'has_electric' => ($row[16] === 'Yes') ? 1 : 0,
                'latitude' => $row[18] ?? null,
                'longitude' => $row[19] ?? null,
                'comments' => $row[17] ?? null,
            ];
        }

        fclose($file);

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('industrial_parks')->insertOrIgnore($chunk);
        }
    }

}
