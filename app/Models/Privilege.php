<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Privilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'group',
        'user',
        'client',
        'app',
        'authorities',
    ];

    protected $hidden = [];

    protected $casts = [
        'authorities' => 'array'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public static function makeAuthorities(array $privilege): array
    {

        $authorities = [];
        foreach ($privilege as $key => $value) {
            if (!$value || !is_string($value)) continue;
            if (strlen($value) > 1) {
                $values = array_map(fn ($authority) => sprintf('%s:%s', $key, $authority), str_split($value));
                $authorities = array_merge($authorities, $values);
                continue;
            }
            $authorities[] = sprintf('%s:%s', $key, $value);
        }
        return $authorities;
    }
}
