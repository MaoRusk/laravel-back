<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:users.show', only: ['show']),
            new Middleware('permission:users.create', only: ['store']),
            new Middleware('permission:users.update', only: ['update']),
            new Middleware('permission:users.destroy', only: ['destroy'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->success(data: User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): \App\Responses\ApiResponse
    {
        try {

            $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);
            if (!empty($validatedData['role_id'])) {
                $user->syncRoles($validatedData['role_id']);
            }
            return $this->success('User created successfully', $user);
        } catch (\Exception $e) {
            return $this->error('Error creating user: ' . $e->getMessage(), status: 500);
        }
    }

    /**
     * @param User $user
     * @return \App\Responses\ApiResponse
     */
    public function show(User $user): \App\Responses\ApiResponse
    {
        return $this->success(data: $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): \Illuminate\Http\JsonResponse
    {
        // Obtener los datos validados
        $validatedData = $request->validated();

        // Si se envió la contraseña, encriptarla
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Elimina la contraseña si no fue enviada
        }

        // Actualizar el usuario con los datos ajustados
        $user->update($validatedData);
        if (!empty($validatedData['role_id'])) {
            $user->syncRoles($validatedData['role_id']);
        }
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): \App\Responses\ApiResponse
    {
        try {
            if ($user->delete()) {
                return $this->success('User deleted successfully');
            }
            return $this->error('User delete failed', status: 422);

        } catch (\Exception $e) {
            return $this->error('Error deleting user: ' . $e->getMessage(), status: 500);
        }
    }
}
