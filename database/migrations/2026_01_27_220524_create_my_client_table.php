<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('my_client', function (Blueprint $table) {
            $table->id();

            $table->char('name', 250);
            $table->char('slug', 100);

            $table->string('is_project', 30)->default('0');
            $table->char('self_capture', 1)->default('1');

            $table->char('client_prefix', 4);
            $table->char('client_logo', 255)->default('no-image.jpg');

            $table->text('address')->nullable();
            $table->char('phone_number', 50)->nullable();
            $table->char('city', 50)->nullable();

            $table->timestamp('created_at', 0)->nullable();
            $table->timestamp('updated_at', 0)->nullable();
            $table->timestamp('deleted_at', 0)->nullable();
        });

        DB::statement("ALTER TABLE my_client ADD CONSTRAINT my_client_is_project_check CHECK (is_project in ('0','1'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE my_client DROP CONSTRAINT IF EXISTS my_client_is_project_check");
        Schema::dropIfExists('my_client');
    }
};
