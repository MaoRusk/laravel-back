<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RichanFongdasen\EloquentBlameable\BlameableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenunt createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenunt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenunt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenunt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenunt updatedBy($userId)
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedBy($value)
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withoutTrashed()
 * @mixin \Eloquent
 */
class Tenant extends Model
{
    use HasFactory, BlameableTrait, SoftDeletes;

    protected $table = 'cat_tenants';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
