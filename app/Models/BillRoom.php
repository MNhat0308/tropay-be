<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $at
 * @property string $old_electric
 * @property string $new_electric
 * @property string $old_water
 * @property string $new_water
 * @property string $price_water
 * @property string $price_electric
 * @property string $price_room
 * @property string $price_garbage
 * @property int $room_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float|null $electric_consumption
 * @property float|null $water_consumption
 * @property float|null $total_price
 * @property string|null $note
 * @property string|null $rent_month
 * @property-read \App\Models\Room $room
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom lastMonthRecord()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom latestRecordByAt()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillRoom withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BillRoom extends Model
{
    use SoftDeletes;

    protected $table = 'bill_rooms';

    protected $fillable = [
        'at',
        'rent_month',
        'old_electric',
        'new_electric',
        'old_water',
        'new_water',
        'price_water',
        'price_electric',
        'price_room',
        'price_garbage',
        'electric_consumption',
        'water_consumption',
        'total_price',
        'room_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    //get lasted record of bill room base on column at
    public function scopeLatestRecordByAt($query)
    {
        return $query->latest('at');
    }

    public function scopeLastMonthRecord($query)
    {
        return $query->whereBetween('at', [now()->firstOfMonth()->subMonth(), now()]);
    }
}
