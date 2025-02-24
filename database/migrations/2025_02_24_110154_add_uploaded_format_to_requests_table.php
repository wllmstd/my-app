<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('uploaded_format')->nullable()->after('Attachment'); // Add the column
        });
    }
    
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('uploaded_format'); // Rollback if needed
        });
    }
    
};