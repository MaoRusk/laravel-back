<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Market;
use App\Models\SubMarket;
use App\Models\PermissionsUnique;
use App\Models\PermissionsSubModules;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class PermissionController extends ApiController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        return $this->response('Permissions obtained successfully', $permissions);
    }

    /*
     * MULTIPLE INSERT
     */
    public function store(Request $request)
    {
        $userId = $request->userId;

        /*
         * Permisos Únicos
         */

        $excelPermission = $request->excelPermission == true ? 1 : 0;
        $flyerPermission = $request->flyerPermission == true ? 1 : 0;
        $columnsPermission = $request->columnsPermission == true ? 1 : 0;
        $fibrasPermission = $request->fibrasPermission == true ? 1 : 0;

        $arrayName = [
            "1" => $excelPermission, // * excel
            "2" => $flyerPermission, // * flyer
            "3" => $columnsPermission, // * columnas
            "4" => $fibrasPermission // * columnas
        ];

        foreach ($arrayName as $key => $permission) {

            $exists = PermissionsUnique::where('userId', $userId)
                    ->where('permissionId', $key)
                    ->exists();

            if (!$exists && $permission == 1) {

                PermissionsUnique::insert([
                    'userId' => $userId,
                    'permissionId' => $key,
                    'status' => 1
                ]);
            }
        }

        /*
         * Permisos BiCharts
         */
        $biChartsPermission = explode(",", $request->biChartsPermission);

        foreach ($biChartsPermission as $key => $module) {

            $exists = PermissionsSubModules::where('userId', $userId)
                    ->where('subModuleId', $module)
                    ->exists();

            if (!$exists) {
                PermissionsSubModules::insert([
                    'userId' => $userId,
                    'subModuleId' => $module,
                    'status' => 1
                ]);
            }
        }

        /*
         * Permisos
         */
        $modulesArray = explode(",", $request->modulesCbo);
        // $marketArray = $request->marketsArray;
        $marketArray = json_decode($request->marketsArray, true);
        $yearsArray = explode(",", $request->yearsCbo);
        $quartersArray = explode(",", $request->quartersCbo);


        foreach ($modulesArray as $key => $moduleId) {

            foreach ($marketArray as $key => $market) {
                $marketId = str_replace("market_", "", $market["marketId"]);
                $submarketId = $market["subMarketId"];

                switch ($moduleId) {
                    case 1:
                    case 5:
                    case 7:
                    case 10:
                    case 14:

                        $exists = Permission::where('userId', $userId)
                                ->where('moduleId', $moduleId)
                                ->where('marketId', $marketId)
                                ->where('subMarketId', $submarketId)
                                ->exists();

                        if (!$exists) {
                            Permission::insert([
                                'userId' => $userId,
                                'moduleId' => $moduleId,
                                'marketId' => $marketId,
                                'subMarketId' => $submarketId,
                                'year' => NULL,
                                'quarter' => NULL,
                                'status' => 'Activo'
                            ]);
                        }
                    break;

                    default:

                        foreach ($yearsArray as $key => $year) {

                            foreach ($quartersArray as $key => $quarter) {

                                $exists = Permission::where('userId', $userId)
                                        ->where('moduleId', $moduleId)
                                        ->where('marketId', $marketId)
                                        ->where('subMarketId', $submarketId)
                                        ->where('year', $year)
                                        ->where('quarter', $quarter)
                                        ->exists();

                                if (!$exists) {
                                    Permission::insert([
                                        'userId' => $userId,
                                        'moduleId' => $moduleId,
                                        'marketId' => $marketId,
                                        'subMarketId' => $submarketId,
                                        'year' => $year,
                                        'quarter' => $quarter,
                                        'status' => 'Activo'
                                    ]);
                                }
                            }
                        }
                    break;
                }
            }
        }

        return response()->json(['message' => 'Permissions added successfully!']);
    }

    /*
     * SHOW INDIVIDUAL PERMISSIONS
     */

    public function showPermissions(Request $request)
    {

        $userId = $request->userId;
        $moduleId = $request->moduleId;
        $year = $request->year;
        $quarter = $request->quarter;

        $mainReturn = [];
        $cleanArray = [];

        switch ($moduleId) {
            case 1:
            case 5:
            case 7:
            case 10:
            case 14:

                $newArray = [];

                // * consultar todos los mercados y submercados existentes
                $allMarkets = Market::select('id', 'marketName')
                    ->where('status', 'Activo')
                    ->get();

                foreach ($allMarkets as $key => $marketData) {

                    $cleanArray = array(
                        'value' => "market_" . $marketData->id,
                        'label' => $marketData->marketName,
                        'selected' => false,
                        'options' => []
                    );

                    $existMarketPermission = Permission::where('userId', $userId)
                        ->where('moduleId', $moduleId)
                        ->where('marketId', $marketData->id)
                        ->where('status', 'Activo')
                        ->exists();

                    if ($existMarketPermission) {
                        $cleanArray['selected'] = true;
                    }

                    $allSubMarkets = SubMarket::select('id', 'subMarketName')
                        ->where('marketId', $marketData->id)
                        ->where('status', 'Activo')
                        ->get();

                    foreach ($allSubMarkets as $key => $subMarketData) {

                        $arrayOptions = array(
                            "value" => $subMarketData->id,
                            "label" => $subMarketData->subMarketName,
                            "selected" => false
                        );

                        $existSubMarketPermission = Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketData->id)
                            ->where('subMarketId', $subMarketData->id)
                            ->where('status', 'Activo')
                            ->exists();

                        if ($existSubMarketPermission) {
                            $arrayOptions['selected'] = true;
                        }

                        array_push($cleanArray['options'], $arrayOptions);
                    }

                    array_push($newArray, $cleanArray);
                }

                $mainReturn['markets'] = $newArray;

            break;

            default:

                // * en caso de no recibir un año válido se sale de la función
                if ($year == 0 || $year == "") {
                    echo "Invalid year";
                    return;
                }

                $newArray = [];

                // * consultar todos los mercados y submercados existentes
                $allMarkets = Market::select('id', 'marketName')
                    ->where('status', 'Activo')
                    ->get();

                foreach ($allMarkets as $key => $marketData) {

                    $cleanArray = array(
                        'value' => "market_" . $marketData->id,
                        'label' => $marketData->marketName,
                        'selected' => false,
                        'options' => []
                    );

                    if ($quarter == "") {

                        // * consultar los quarters disponibles
                        $getQuarters = Permission::select('quarter')
                            ->where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('year', $year)
                            ->where('status', 'Activo')
                            ->distinct()
                            ->get();

                        return response()->json($getQuarters);

                        $quarter = [];
                        $contador = 0;

                        foreach ($getQuarters as $key => $quarterValue) {
                            array_push($quarter, $quarterValue->quarter);
                            $contador++;
                        }

                        if ($contador <= 3) {
                            return;
                        }

                        $existMarketPermission = Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketData->id)
                            ->where('year', $year)
                            ->whereIn('quarter', $quarter)
                            ->where('status', 'Activo')
                            ->exists();

                    } else {

                        $existMarketPermission = Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketData->id)
                            ->where('year', $year)
                            ->where('quarter', $quarter)
                            ->where('status', 'Activo')
                            ->exists();
                    }

                    if ($existMarketPermission) {
                        $cleanArray['selected'] = true;
                    }

                    $allSubMarkets = SubMarket::select('id', 'subMarketName')
                        ->where('marketId', $marketData->id)
                        ->where('status', 'Activo')
                        ->get();

                    foreach ($allSubMarkets as $key => $subMarketData) {

                        $arrayOptions = array(
                            "value" => $subMarketData->id,
                            "label" => $subMarketData->subMarketName,
                            "selected" => false
                        );

                        $existSubMarketPermission = Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketData->id)
                            ->where('subMarketId', $subMarketData->id)
                            ->where('year', $year)
                            ->where('quarter', $quarter)
                            ->where('status', 'Activo')
                            ->exists();

                        if ($existSubMarketPermission) {
                            $arrayOptions['selected'] = true;
                        }

                        array_push($cleanArray['options'], $arrayOptions);
                    }

                    array_push($newArray, $cleanArray);
                }

                $mainReturn['markets'] = $newArray;

            break;
        }

        return response()->json($mainReturn);
    }

    /*
     * UPDATE SINGLE PERMISSIONS
    */
    public function updatePermissions(Request $request)
    {
        $userId = $request->userId;
        $moduleId = $request->module;
        $year = $request->year;
        $quarter = $request->quarter;
        $marketsArray = $request->marketsArray;

        // * Si el quarter está vacío se da por entender que los cambios se aplicaran a los 4 quarters...
        if ($quarter == "") {
            $quarter = ['Q1', 'Q2', 'Q3', 'Q4'];
        }

        // * separando mercados y submercados
        $justMarkets = [];
        $justSubMarkets = [];

        foreach ($marketsArray as $key => $marketPermissions) {

            if (!in_array($marketPermissions['subMarketId'], $justSubMarkets)) {
                array_push($justSubMarkets, $marketPermissions['subMarketId']);
            }

            if (!in_array(str_replace("market_", "", $marketPermissions['marketId']), $justMarkets)) {
                array_push($justMarkets, str_replace("market_", "", $marketPermissions['marketId']));
            }
        }

        switch ($moduleId) {
            case 1:
            case 5:
            case 7:
            case 10:
            case 14: // * Módulos de disponibilidad

                // * consultar todos los mercados y submercados existentes
                $allMarkets = Market::select('id', 'marketName')
                    ->where('status', 'Activo')
                    ->get();

                foreach ($allMarkets as $key => $marketData) {

                    // * Si el mercado está en el post se hace el update
                    if (in_array($marketData->id, $justMarkets)) {

                        // * Obtener los submercados
                        $allSubMarkets = SubMarket::select('id', 'subMarketName')
                            ->where('marketId', $marketData->id)
                            ->where('status', 'Activo')
                            ->get();

                        foreach ($allSubMarkets as $key => $subMarketData) {

                            // * Si el submercado está en el post se hace el update
                            if (in_array($subMarketData->id, $justSubMarkets)) {

                                $existSubMarketPermission = Permission::where('userId', $userId)
                                    ->where('moduleId', $moduleId)
                                    ->where('marketId', $marketData->id)
                                    ->where('subMarketId', $subMarketData->id)
                                    ->exists();

                                if ($existSubMarketPermission) {

                                    Permission::where('userId', $userId)
                                        ->where('moduleId', $moduleId)
                                        ->where('marketId', $marketData->id)
                                        ->where('subMarketId', $subMarketData->id)
                                        ->update(['status' => 'Activo']);

                                } else {

                                    Permission::insert([
                                        'userId' => $userId,
                                        'moduleId' => $moduleId,
                                        'marketId' => $marketData->id,
                                        'subMarketId' => $subMarketData->id,
                                        'year' => NULL,
                                        'quarter' => NULL,
                                        'status' => 'Activo'
                                    ]);
                                }

                            } else {

                                $existSubMarketPermission = Permission::where('userId', $userId)
                                    ->where('moduleId', $moduleId)
                                    ->where('marketId', $marketData->id)
                                    ->where('subMarketId', $subMarketData->id)
                                    ->exists();

                                if ($existSubMarketPermission) {

                                    Permission::where('userId', $userId)
                                        ->where('moduleId', $moduleId)
                                        ->where('marketId', $marketData->id)
                                        ->where('subMarketId', $subMarketData->id)
                                        ->update(['status' => 'Inactivo']);

                                }
                            }
                        }

                    } else {

                        $existMarketPermission = Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketData->id)
                            ->exists();

                        if($existMarketPermission){
                            Permission::where('userId', $userId)
                                ->where('moduleId', $moduleId)
                                ->where('marketId', $marketData->id)
                                ->update(['status' => 'Inactivo']);
                        }
                    }
                }

            break;

            default: // * Módulos de absorción

                // * consultar todos los mercados y submercados existentes
                $allMarkets = Market::select('id', 'marketName')
                    ->where('status', 'Activo')
                    ->get();

                foreach ($allMarkets as $key => $marketData) {

                    // * Si el mercado está en el post se hace el update
                    if (in_array($marketData->id, $justMarkets)) {

                        // * Obtener los submercados
                        $allSubMarkets = SubMarket::select('id', 'subMarketName')
                            ->where('marketId', $marketData->id)
                            ->where('status', 'Activo')
                            ->get();

                        foreach ($allSubMarkets as $key => $subMarketData) {

                            // * Si el submercado está en el post se hace el update
                            if (in_array($subMarketData->id, $justSubMarkets)) {

                                if (is_array($quarter)) {

                                    foreach ($quarter as $key => $stringQuarter) {

                                        $existSubMarketPermission = Permission::where('userId', $userId)
                                            ->where('moduleId', $moduleId)
                                            ->where('marketId', $marketData->id)
                                            ->where('subMarketId', $subMarketData->id)
                                            ->where('year', $year)
                                            ->where('quarter', $stringQuarter)
                                            ->exists();

                                        if ($existSubMarketPermission) {

                                            Permission::where('userId', $userId)
                                                ->where('moduleId', $moduleId)
                                                ->where('marketId', $marketData->id)
                                                ->where('subMarketId', $subMarketData->id)
                                                ->where('year', $year)
                                                ->where('quarter', $stringQuarter)
                                                ->update(['status' => 'Activo']);

                                        } else {

                                            Permission::insert([
                                                'userId' => $userId,
                                                'moduleId' => $moduleId,
                                                'marketId' => $marketData->id,
                                                'subMarketId' => $subMarketData->id,
                                                'year' => $year,
                                                'quarter' => $stringQuarter,
                                                'status' => 'Activo'
                                            ]);
                                        }
                                    }

                                } else {

                                    $existSubMarketPermission = Permission::where('userId', $userId)
                                            ->where('moduleId', $moduleId)
                                            ->where('marketId', $marketData->id)
                                            ->where('subMarketId', $subMarketData->id)
                                            ->where('year', $year)
                                            ->where('quarter', $quarter)
                                            ->exists();

                                    if ($existSubMarketPermission) {

                                        Permission::where('userId', $userId)
                                            ->where('moduleId', $moduleId)
                                            ->where('marketId', $marketData->id)
                                            ->where('subMarketId', $subMarketData->id)
                                            ->where('year', $year)
                                            ->where('quarter', $quarter)
                                            ->update(['status' => 'Activo']);

                                    } else {

                                        Permission::insert([
                                            'userId' => $userId,
                                            'moduleId' => $moduleId,
                                            'marketId' => $marketData->id,
                                            'subMarketId' => $subMarketData->id,
                                            'year' => $year,
                                            'quarter' => $quarter,
                                            'status' => 'Activo'
                                        ]);
                                    }
                                }

                            } else {

                                $existSubMarketPermission = Permission::where('userId', $userId)
                                    ->where('moduleId', $moduleId)
                                    ->where('marketId', $marketData->id)
                                    ->where('subMarketId', $subMarketData->id)
                                    ->where('year', $year)
                                    ->where('quarter', $quarter)
                                    ->exists();

                                if ($existSubMarketPermission) {

                                    Permission::where('userId', $userId)
                                        ->where('moduleId', $moduleId)
                                        ->where('marketId', $marketData->id)
                                        ->where('subMarketId', $subMarketData->id)
                                        ->where('year', $year)
                                        ->where('quarter', $quarter)
                                        ->update(['status' => 'Inactivo']);

                                }
                            }
                        }

                    } else {

                        if (is_array($quarter)) {

                            foreach ($quarter as $key => $stringQuarter) {
                                $existMarketPermission = Permission::where('userId', $userId)
                                ->where('moduleId', $moduleId)
                                ->where('marketId', $marketData->id)
                                ->where('year', $year)
                                ->where('quarter', $stringQuarter)
                                ->exists();

                                if($existMarketPermission){
                                    Permission::where('userId', $userId)
                                        ->where('moduleId', $moduleId)
                                        ->where('marketId', $marketData->id)
                                        ->where('year', $year)
                                        ->where('quarter', $stringQuarter)
                                        ->update(['status' => 'Inactivo']);
                                }
                            }

                        } else {

                            $existMarketPermission = Permission::where('userId', $userId)
                                ->where('moduleId', $moduleId)
                                ->where('marketId', $marketData->id)
                                ->where('year', $year)
                                ->where('quarter', $quarter)
                                ->exists();

                            if($existMarketPermission){
                                Permission::where('userId', $userId)
                                    ->where('moduleId', $moduleId)
                                    ->where('marketId', $marketData->id)
                                    ->where('year', $year)
                                    ->where('quarter', $quarter)
                                    ->update(['status' => 'Inactivo']);
                            }
                        }
                    }
                }

            break;
        }
    }

    /*
     * HEREDAR PERMISOS
    */
    public function clonePermissions(Request $request)
    {
        $userId = $request->userId;
        $userCloneId = $request->userCloneId;

        // * Primero "eliminamos" los permisos actuales
        Permission::where('userId', $userId)->update(['status' => 'Inactivo']);

        // * Obteniendo los permisos a heredar del otro usuario
        $userClonePermissions = Permission::select('moduleId', 'marketId', 'subMarketId', 'year', 'quarter', 'status')
            ->where('userId', $userCloneId)
            ->get();
        // return response()->json($userClonePermissions);
        // echo "| moduleID | marketId | subMarketId | year | quarter | status | \n";
        // echo "============ \n";
        foreach ($userClonePermissions as $key => $permissionsData) {

            $moduleId = $permissionsData->moduleId;
            $marketId = $permissionsData->marketId;
            $subMarketId = $permissionsData->subMarketId;
            $year = $permissionsData->year;
            $quarter = $permissionsData->quarter;
            $status = $permissionsData->status;
            // echo "| $moduleID | $marketId | $subMarketId | $year | $quarter | $status | \n";
            // echo "============ \n";

            switch ($moduleId) {
                case 1:
                case 5:
                case 7:
                case 10:
                case 14: // * Módulos de disponibilidad
                    $exist = Permission::where('userId', $userId)
                        ->where('moduleId', $moduleId)
                        ->where('marketId', $marketId)
                        ->where('subMarketId', $subMarketId)
                        ->exists();

                    if($exist){
                        Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketId)
                            ->where('subMarketId', $subMarketId)
                            ->update(['status' => 'Activo']);
                    } else {
                        Permission::insert([
                            'userId' => $userId,
                            'moduleId' => $moduleId,
                            'marketId' => $marketId,
                            'subMarketId' => $subMarketId,
                            'status' => 'Activo'
                        ]);
                    }
                break;

                default:
                    $exist = Permission::where('userId', $userId)
                        ->where('moduleId', $moduleId)
                        ->where('marketId', $marketId)
                        ->where('subMarketId', $subMarketId)
                        ->where('year', $year)
                        ->where('quarter', $quarter)
                        ->exists();

                    if($exist){
                        Permission::where('userId', $userId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketId)
                            ->where('subMarketId', $subMarketId)
                            ->where('year', $year)
                            ->where('quarter', $quarter)
                            ->update(['status' => 'Inactivo']);
                    } else {
                        Permission::insert([
                            'userId' => $userId,
                            'moduleId' => $moduleId,
                            'marketId' => $marketId,
                            'subMarketId' => $subMarketId,
                            'year' => $year,
                            'quarter' => $quarter,
                            'status' => 'Activo'
                        ]);
                    }
                break;
            }
        }
    }


    /*
     * HEREDAR A MULTIPLES EMPLEADOS
    */

    public function cloneMultipleUsers(Request $request)
    {

        $userMain = $request->userId;
        $userClones = explode(",", $request->userClones);

        // * Validando si el usuario principal tiene permisos asignados
        $existUserMain = Permission::where('userId', $userMain)
            /* ->where('status', 'Activo') */
            ->exists();

        if (!$existUserMain) {
            return response()->json([
                'title' => 'Error',
                'text' => "This user doesn't have permissions",
                'icon' => 'error'
            ]);
        }

        // * Obteniendo los permisos a heredar del otro usuario
        $userClonePermissions = Permission::select('moduleId', 'marketId', 'subMarketId', 'year', 'quarter', 'status')
            ->where('userId', $userMain)
            ->where('status', 'Activo')
            ->get();

        foreach ($userClones as $key => $userCloneId) {

            // * Primero "eliminamos" los permisos actuales
            Permission::where('userId', $userCloneId)->update(['status' => 'Inactivo']);

            foreach ($userClonePermissions as $key => $permissionsData) {

                $moduleId = $permissionsData->moduleId;
                $marketId = $permissionsData->marketId;
                $subMarketId = $permissionsData->subMarketId;
                $year = $permissionsData->year;
                $quarter = $permissionsData->quarter;
                // $status = $permissionsData->status;

                switch ($moduleId) {
                    case 1:
                    case 5:
                    case 7:
                    case 10:
                    case 14: // * Módulos de disponibilidad
                        $exist = Permission::where('userId', $userCloneId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketId)
                            ->where('subMarketId', $subMarketId)
                            ->exists();

                        if($exist){
                            Permission::where('userId', $userCloneId)
                                ->where('moduleId', $moduleId)
                                ->where('marketId', $marketId)
                                ->where('subMarketId', $subMarketId)
                                ->update(['status' => 'Activo']);
                        } else {
                            Permission::insert([
                                'userId' => $userCloneId,
                                'moduleId' => $moduleId,
                                'marketId' => $marketId,
                                'subMarketId' => $subMarketId,
                                'status' => 'Activo'
                            ]);
                        }
                    break;

                    default:
                        $exist = Permission::where('userId', $userCloneId)
                            ->where('moduleId', $moduleId)
                            ->where('marketId', $marketId)
                            ->where('subMarketId', $subMarketId)
                            ->where('year', $year)
                            ->where('quarter', $quarter)
                            ->exists();

                        if($exist){
                            Permission::where('userId', $userCloneId)
                                ->where('moduleId', $moduleId)
                                ->where('marketId', $marketId)
                                ->where('subMarketId', $subMarketId)
                                ->where('year', $year)
                                ->where('quarter', $quarter)
                                ->update(['status' => 'Inactivo']);
                        } else {
                            Permission::insert([
                                'userId' => $userCloneId,
                                'moduleId' => $moduleId,
                                'marketId' => $marketId,
                                'subMarketId' => $subMarketId,
                                'year' => $year,
                                'quarter' => $quarter,
                                'status' => 'Activo'
                            ]);
                        }
                    break;
                }
            }
        }

        return response()->json([
            'title' => 'Completed',
            'text' => "Permissions Cloned.",
            'icon' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // * Buscar permisos en cada tabla
        $permissionsModule = DB::table('UserPermissionsModule')->where('userId', $id)->get();
        $permissionsMarket = DB::table('UserPermissionsMarket')->where('userId', $id)->get();
        $permissionsSubmarket = DB::table('UserPermissionsSubmarket')->where('userId', $id)->get();
        $permissionsQuarters = DB::table('UserPermissionsQuarters')
            ->where('userId', $id)
            // ->select('id', 'userId', 'year', 'quarter', 'status')
            ->select('userId', 'year', 'quarter', 'status')
            ->orderBy('year', 'asc')
            ->get();
        // // $permissionsYears = DB::table('UserPermissionsYears')->where('userId', $id)->get();

        // * Verificar si no se encontraron permisos en ninguna tabla
        if ($permissionsModule->isEmpty() && $permissionsMarket->isEmpty() && $permissionsSubmarket->isEmpty() && $permissionsQuarters->isEmpty()) {
            return response()->json(['message' => 'Permissions not found'], 404);
        }

        // * Organizar los permisos por año y quarters
        $organizedPermissions = [];

        foreach ($permissionsQuarters as $permission) {

            $year = $permission->year;

            if (!isset($organizedPermissions[$year])) {
                $organizedPermissions[$year] = [
                    // 'id' => $permission->id,
                    'userId' => $permission->userId,
                    'year' => $year,
                    'status' => $permission->status,
                    'quarters' => []
                ];
            }

            $organizedPermissions[$year]['quarters'][] = $permission->quarter;
        }

        // * Convertir la estructura asociativa en un array indexado
        $quartersArraySort = array_values($organizedPermissions);

        // * Construir la respuesta con los permisos de cada tabla
        $permissions = [
            'module' => $permissionsModule,
            'market' => $permissionsMarket,
            'submarket' => $permissionsSubmarket,
            'years' => $quartersArraySort,
        ];

        return response()->json($permissions);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'moduleId' => 'required|exists:modules,id',
            'marketId' => 'required|exists:markets,id',
            'subMarketId' => 'required|exists:sub_markets,id',
            'year' => 'required|integer',
            'quarter' => 'required|integer',
            'status' => 'required|in:Activo,Inactivo',
        ]);

        $permissions = Permission::findOrFail($id);
        $permissions->update($request->all());

        return response()->json($permissions);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permissions = Permission::findOrFail($id);

        $permissions->status = 'Inactivo';
        $permissions->save();

        return response()->json(['message' => 'Permiso eliminado correctamente']);
    }
}
