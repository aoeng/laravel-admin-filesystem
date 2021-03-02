<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filesystems', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('disk');
            $table->string('path');
            $table->string('name');
            $table->string('ext');
            $table->unsignedBigInteger('size')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('disk');
            $table->index('type');
            $table->index(['disk', 'type']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filesystems');
    }
}
