<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('neighbors', function (Blueprint $table) {

          
            $table->decimal('credit_limit_bs', 12, 2)->default(0)->after('address');
            $table->decimal('credit_limit_usd', 12, 2)->default(0)->after('credit_limit_bs');
        });
    }

    public function down(): void
    {
        Schema::table('neighbors', function (Blueprint $table) {
            $table->dropColumn(['credit_limit_bs','credit_limit_usd']);
        });
    }
};
