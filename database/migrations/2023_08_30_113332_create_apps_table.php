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
            $table->integer('application')->default(0);
            $table->string('path')->unique();
            $table->string('subdomain')->unique();
            $table->unsignedInteger('grpc_port')->unique();
            $table->boolean('use_domain')->default(false);
            $table->string('domain')->nullable();
            $table->boolean('use_custom')->default(false);
            $table->string('redis_host')->nullable();
            $table->integer('redis_port')->nullable();
            $table->string('memcached_host')->nullable();
            $table->enum('db_type', ['mysql', 'pgsql', 'sqlite', 'mongodb'])->nullable();
            $table->string('db_name');
            $table->string('db_host')->nullable();
            $table->integer('db_port')->nullable();
            $table->enum('cache_driver', ['redis', 'memcached', 'file'])->nullable();
            $table->enum('session_driver', ['redis', 'memcached', 'file'])->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->unique(['db_type', 'db_name', 'db_host', 'db_port']);
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
