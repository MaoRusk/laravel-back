<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('submodules', function (Blueprint $table) {
            $table->id();
            $table->string('submodule_name');
            $table->foreignId('module_id')->constrained('modules');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // Crear la tabla access_policies
        Schema::create('access_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
        
        Schema::create('permissions_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_policy_id')->constrained('access_policies');
            $table->foreignId('module_id')->constrained('modules');
            $table->foreignId('market_id')->constrained('cat_markets');
            $table->foreignId('sub_market_id')->constrained('cat_sub_markets');
            $table->integer('year')->nullable();
            $table->string('quarter')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        Schema::create('permissions_unique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_policy_id')->constrained('access_policies');
            $table->foreignId('permission_id')->constrained('permissions_policies');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        Schema::create('permissions_submodules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_policy_id')->constrained('access_policies');
            $table->foreignId('submodule_id')->constrained('submodules');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('access_policies');
        Schema::dropIfExists('submodules');
        Schema::dropIfExists('permissions_submodules');
        Schema::dropIfExists('permissions_unique');
        Schema::dropIfExists('permissions_policies');
    }
};