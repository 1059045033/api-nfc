<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfcData extends Model
{
    public $timestamps = false;
    protected $table = 'nfc_data';
    protected $fillable = [
        'nfc_id',
        'wechat_id',
        'data_type',
        'data_content',
        'title',
        'remarks',
        'created_at'
    ];
}
