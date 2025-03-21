<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property int $market_id
 * @property int $submarket_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Market $market
 * @property-read \App\Models\SubMarket $subMarket
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereMarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereSubmarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereUpdatedBy($value)
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndustrialPark withoutTrashed()
 * @mixin \Eloquent
 */
class IndustrialPark extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_industrial_parks';

    protected $fillable = [
        'name',
        'market_id',
        'submarket_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function market(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function subMarket(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SubMarket::class, 'submarket_id');
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
