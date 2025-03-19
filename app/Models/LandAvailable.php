<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RichanFongdasen\EloquentBlameable\BlameableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 *
 *
 * @property int $id
 * @property int $land_id
 * @property int|null $abs_company_id
 * @property int|null $avl_broker_id
 * @property int|null $abs_country_id
 * @property int|null $abs_broker_id
 * @property string $land_state
 * @property int|null $avl_size_sm
 * @property string|null $avl_land_sm
 * @property int|null $avl_minimum_size_ha
 * @property string|null $avl_min_sale
 * @property string|null $avl_max_sale
 * @property string|null $avl_zoning
 * @property string|null $avl_pacel_shape
 * @property int|null $avl_rail_spur
 * @property int|null $avl_natural_gas
 * @property int|null $avl_sewage
 * @property int|null $avl_water
 * @property int|null $avl_electric
 * @property int|null $avl_conditioned_construction
 * @property int|null $avl_quarter
 * @property int|null $avl_year
 * @property string|null $land_condition
 * @property int|null $abs_size_HA
 * @property int|null $abs_quarter
 * @property int|null $abs_year
 * @property string|null $abs_closing_price
 * @property string|null $abs_type_buyer
 * @property string|null $abs_company_type
 * @property string|null $comments
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Developer|null $absBroker
 * @property-read \App\Models\Company|null $absCompany
 * @property-read \App\Models\Country|null $absCountry
 * @property-read \App\Models\Developer|null $avlBroker
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Land $land
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable createdBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable updatedBy($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsBrokerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsClosingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsCompanyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsQuarter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsSizeHA($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsTypeBuyer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlBrokerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlConditionedConstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlElectric($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlLandSm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlMaxSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlMinSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlNaturalGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlPacelShape($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlQuarter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlRailSpur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlSewage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlSizeSm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlWater($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlZoning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereLandCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereLandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereLandState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable withoutTrashed()
 * @property int|null $abs_industry_id
 * @property int|null $avl_size_ha
 * @property int|null $kvas
 * @property int|null $abs_size_ha
 * @property string|null $abs_kvas_price
 * @property int|null $rail_spur
 * @property string|null $avl_comments
 * @property string|null $abs_comments
 * @property string $state
 * @property string|null $natural_gas
 * @property string|null $sewage
 * @property string|null $water
 * @property string|null $electric
 * @property string|null $abs_final_use
 * @property string $avl_deal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsFinalUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsKvasPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAbsSizeHa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlDeal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereAvlSizeHa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereElectric($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereKvas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereNaturalGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereRailSpur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereSewage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandAvailable whereWater($value)
 * @property-read \App\Models\Industry|null $absIndustry
 * @mixin \Eloquent
 */
class LandAvailable extends Model
{
    use HasFactory, BlameableTrait, SoftDeletes;

    protected $table = 'lands_available';

    protected $fillable = [
        'land_id',
        'land_condition',
        'rail_spur',
        'natural_gas',
        'sewage',
        'water',
        'electric',
        'kvas',
        'avl_broker_id',
        'avl_size_ha',
        'avl_minimum_size_ha',
        'avl_min_sale',
        'avl_max_sale',
        'avl_conditioned_construction',
        'avl_deal',
        'avl_comments',
        'abs_company_id',
        'abs_country_id',
        'abs_size_ha',
        'abs_closing_price',
        'abs_type_buyer',
        'abs_company_type',
        'abs_industry_id',
        'abs_final_use',
        'abs_broker_id',
        'abs_comments',
        'abs_kvas_price',
        'state',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function land(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Land::class, 'land_id');
    }

    public function absCompany(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class, 'abs_company_id');
    }

    public function avlBroker(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Broker::class, 'avl_broker_id');
    }

    public function absCountry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'abs_country_id');
    }

    public function absBroker(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Broker::class, 'abs_broker_id');
    }

    public function absIndustry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Industry::class, 'abs_industry_id');
    }

}
