<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id('Request_ID'); // Auto-increment primary key
            $table->string('Status');
            $table->string('First_Name');
            $table->string('Last_Name');
            $table->string('Nationality');
            $table->string('Location');
            $table->string('Format');
            $table->string('Attachment')->nullable();
            $table->timestamp('Date_Created')->useCurrent();
            $table->timestamp('Updated_Time')->useCurrent()->useCurrentOnUpdate();
            $table->foreignId('Users_ID')->constrained('users')->onDelete('cascade'); // Foreign key
        });
    }

    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
