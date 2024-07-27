<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompositeProduct extends Model
{

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'composite_id', 'simple_id' , 'amount'
    ];

    //relacionamento N:N
    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'composite_id');
    
    }

    public function simple() {
        return $this->hasOne(Product::class, 'id', 'simple_id');
    }
}
