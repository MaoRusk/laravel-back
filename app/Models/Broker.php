<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RichanFongdasen\EloquentBlameable\BlameableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker updatedBy($userId)
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereUpdatedBy($value)
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Broker withoutTrashed()
 * @mixin \Eloquent
 */
class Broker extends Model
{
    use HasFactory, BlameableTrait, SoftDeletes;

    protected $table = 'cat_brokers';

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
