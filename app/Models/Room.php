<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property float $price_water
 * @property float $price_electric
 * @property float $price_room
 * @property float $price_garbage
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BillRoom> $billRooms
 * @property-read int|null $bill_rooms_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|Room withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Room withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price_water',
        'price_electric',
        'price_room',
        'price_garbage',
    ];

    public function billRooms(): HasMany
    {
        return $this->hasMany(BillRoom::class, 'room_id');
    }
}
