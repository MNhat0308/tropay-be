<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int $room_id
 * @property \Illuminate\Support\Carbon|null $dob
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $gender
 * @property string|null $identification
 * @property \Illuminate\Support\Carbon|null $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property string|null $status
 * @property array|null $addition_information
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $files
 * @property-read \App\Models\Room $room
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'room_id',
        'dob',
        'address',
        'identification',
        'gender',
        'addition_information',
        'start',
        'end',
        'status',
        'note',
        'phone',
        'files',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    protected function casts()
    {
        return [
            'dob' => 'date',
            'addition_information' => 'array',
            'start' => 'date',
            'end' => 'date',
            'files' => 'array',
        ];
    }
}
