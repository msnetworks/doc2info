<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeroCasesTable extends Migration
{
    public function up()
    {
        Schema::create('hero_cases', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->unique();
            $table->string('product');
            $table->string('profiling')->nullable();
            $table->string('employer_business_name')->nullable();
            $table->string('fi_cpv_type')->nullable();
            $table->string('state');
            $table->string('customer_name');
            $table->text('customer_address')->nullable();
            $table->string('mobile_no');
            $table->string('alt_mob_no')->nullable();
            $table->string('email_id')->nullable();
            $table->decimal('loan_amount', 15, 2);
            $table->string('ownership_type')->default('Individual');
            $table->text('remarks')->nullable();
            $table->integer('status')->default(0); // 1 = Active, 0 = Inactive
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes for better performance
            $table->index('ref_no');
            $table->index('customer_name');
            $table->index('mobile_no');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hero_cases');
    }
}