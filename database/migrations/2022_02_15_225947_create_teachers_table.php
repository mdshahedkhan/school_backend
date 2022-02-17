<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('create_by');
            $table->string('name')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->enum('religion', ['islam', 'hindu', 'christian'])->default('islam');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('uu_id')->unique();
            $table->text('address');
            $table->date('date_of_birth');
            $table->date('join_date');
            $table->string('photo');
            $table->string('username')->unique();
            $table->string('password', 255);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->foreign('create_by')->on('users')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
};
