<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_admin',
        'privilege',
        'authorities',
    ];

    protected $hidden = [];

    protected $casts = [
        'privilege' => 'array',
        'authorities' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public static function makeAuthorities(array $privileges): array
    {

        $authorities = [];
        foreach ($privileges as $key => $value) {
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
