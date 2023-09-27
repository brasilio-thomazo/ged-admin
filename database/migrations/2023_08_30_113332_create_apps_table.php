<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained();
            $table->enum('application', ['client', 'agent']);
            $table->string('domain');
            $table->integer('http_port')->default(80);
            $table->string('redis_host');
            $table->integer('redis_port')->default(6379);
            $table->string('memcached_host')->nullable();
            $table->enum('db_type', ['mysql', 'pgsql', 'sqlite']);
            $table->string('db_name');
            $table->string('db_host');
            $table->integer('db_port');
            $table->enum('cache_driver', ['redis', 'memcached', 'file'])->default('redis');
            $table->enum('session_driver', ['redis', 'memcached', 'file'])->default('redis');
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->unique(['db_type', 'db_name', 'db_host', 'db_port']);
            $table->unique(['domain', 'http_port']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
