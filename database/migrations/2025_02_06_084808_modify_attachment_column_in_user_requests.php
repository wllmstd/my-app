<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->json('Attachment')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->string('Attachment')->nullable()->change(); // Reverting back to string if needed
        });
    }
};
