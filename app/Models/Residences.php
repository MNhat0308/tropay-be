<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $tenant_id
 * @property \Illuminate\Support\Carbon|null $at
 * @property string|null $lookup_code
 * @property string|null $status_id
 * @property array|null $addition_information
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $files
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\Tenant $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Residences newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Residences newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Residences onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Residences query()
 * @method static \Illuminate\Database\Eloquent\Builder|Residences withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Residences withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Residences extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'at',
        'lookup_code',
        'addition_information',
        'files',
        'status_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected function casts()
    {
        return [
            'at' => 'date',
            'addition_information' => 'array',
            'files' => 'array',
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
