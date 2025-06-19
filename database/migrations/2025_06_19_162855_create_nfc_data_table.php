<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfcDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfc_data', function (Blueprint $table) {
            $table->id();
            $table->string('nfc_id');          // NFC ID
            $table->string('wechat_id');       // 微信号
            $table->tinyInteger('data_type');  // 数据类型 (1-4)
            $table->string('data_content');    // 数据内容 (URL 或文字)
            $table->string('title');           // 标题
            $table->text('remarks')->nullable(); // 备注 (可选)
            $table->timestamp('created_at');   // 时间戳
            // 索引优化查询性能
            $table->index('nfc_id');
            $table->index('wechat_id');
            $table->index(['wechat_id', 'data_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfc_data');
    }
}
