<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class App extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'application',
        'path',
        'subdomain',
        'grpc_port',
        'use_domain',
        'domain',
        'use_custom',
        'memcached_host',
        'http_port',
        'redis_host',
        'redis_port',
        'db_type',
        'db_host',
        'db_name',
        'db_port',
        'cache_driver',
        'session_driver',
        'started_at',
        'installed_at',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
