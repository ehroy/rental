<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('name');
            $table->string('tag')->nullable();
            $table->string('icon')->nullable();
            $table->string('logo')->nullable();
            $table->string('action_button_text')->default('Pesan');
            $table->string('no_whatsapp')->default('6287857580910');
            $table->string('wa_message')->default('Hallo, saya tertarik dengan produk ini !');
            $table->string('meta_author')->default('@jstalinko');
            $table->string('meta_description')->default("build with @jstalinko/justorange");
            $table->text('meta_keywords')->default('furniture');
            $table->longText('additional_meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
