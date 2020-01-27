<?php

namespace App\Model;


use Illuminate\database\Eloquent\Model;

class Payment extends Model {
    public $table = 'payment_gateway';

    protected $fillable = array('method');
    public function pembayaran(){
        return $this->hasMany(Payment::class,'id_payment');
    }
}