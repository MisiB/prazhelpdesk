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
        Schema::table('users', function (Blueprint $table) {
            $table->string('workos_id')->nullable()->unique()->after('remember_token');
            $table->string('workos_connection_id')->nullable()->after('workos_id');
            $table->string('workos_connection_type')->nullable()->after('workos_connection_id');
            $table->string('workos_organization_id')->nullable()->after('workos_connection_type');
            $table->string('workos_directory_user_id')->nullable()->after('workos_organization_id');
            $table->text('workos_raw_attributes')->nullable()->after('workos_directory_user_id');
            $table->index('workos_id');
            $table->index('workos_organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['workos_id']);
            $table->dropIndex(['workos_organization_id']);
            $table->dropColumn([
                'workos_id',
                'workos_connection_id',
                'workos_connection_type',
                'workos_organization_id',
                'workos_directory_user_id',
                'workos_raw_attributes',
            ]);
        });
    }
};






