<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices__details', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50);
            $table->foreignId('id_Invoice')->constrained('invoices', 'id')->cascadeOnDelete();
            $table->string('product', 50);
            $table->string('Section', 999);
            $table->string('Status', 50);
            $table->integer('Value_Status');
            $table->date('Payment_Date')->nullable();
            $table->text('note')->nullable();
            $table->string('user',300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices__details');
    }
}
