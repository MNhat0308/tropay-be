<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $vi_name
 * @property string|null $description
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Status withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Status extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'vi_name',
        'description',
        'type',
    ];
}
