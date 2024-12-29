<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên sản phẩm
            $table->text('description')->nullable(); // Mô tả sản phẩm
            $table->decimal('price', 10, 2); // Giá sản phẩm
            $table->integer('quantity'); // Số lượng tồn kho
            $table->timestamps();

            $table->unsignedBigInteger('user_id'); // ID của người dùng
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Xóa khóa ngoại trước khi xóa bảng
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Xóa bảng products
        Schema::dropIfExists('products');
    }
}
