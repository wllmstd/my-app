<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->text('feedback')->nullable()->after('Updated_Time'); // ✅ Add feedback column
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('feedback'); // ✅ Remove feedback column if rolled back
        });
    }
};
