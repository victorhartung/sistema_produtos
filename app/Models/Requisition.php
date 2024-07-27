<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Requisition extends Model
{

       /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected $fillable = ['id', 'user_id', 'product_id', 'amount', 'requisition_date', 'is_exit'];


 

    //relacionamento com usuário
    public function user() {
        return $this->belongsTo(User::class);
    }

   //relacionamento com produto
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
