<?php

namespace App\Models;

// use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use RichanFongdasen\EloquentBlameable\BlameableTrait;
use Spatie\Permission\Traits\HasRoles;

# use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes, BlameableTrait;

    protected $table = 'list_users';

    protected $fillable = [
        'name',
        'lastName',
        'middleName',
        'userName',
        'password',
        'companyId',
        'userTypeId',
        'totalScreens',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    // * Definir columnas de marca de tiempo personalizadas
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'modifiedAt';

    public function usertypes()
    {
        return $this->belongsTo(Usertype::class, 'userTypeId');
    }
}
