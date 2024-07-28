<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Stock extends Model
{

     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected $fillable = ['product_id', 'amount']; 

    //relacionamento com produto
    public function product() {
        
        return $this->belongsTo(Product::class);
    
    }
}
