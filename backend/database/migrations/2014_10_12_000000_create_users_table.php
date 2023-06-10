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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id', false, true)
                ->nullable(false)
                ->comment('company identifier');

            $table->string('name', 255)
                ->nullable(false)
                ->comment('user name');

            $table->string('email', 510)
                ->unique()
                ->nullable(false)
                ->comment('user email');

            $table->integer('email_verified_at', false, true)
                ->default(0);

            $table->string('password');

            $table->rememberToken();

            $table->integer('created_at', false, true)
                ->default(0);

            $table->integer('updated_at', false, true)
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
