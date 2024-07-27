<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $fillable = ['name', 'cost_price', 'retail_price', 'composite'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */

    protected $casts = [
        'is_composite' => 'boolean',
    ];

   
    public function requisitions(){
        
        return $this->hasMany(Requisition::class);
    
    }
    
    public function stocks(){
        return $this->hasMany(Stock::class);
    }

    public function compositeProducts() {
        return $this->hasMany(CompositeProduct::class, 'composite_id', 'id');
    }

    public function productsFather(){
        
        return $this->belongsToMany(CompositeProduct::class, 'simple_id', 'id');

    }

     /**
     * Mutator para calculo de custo dos produtos compostos
     *
     * @return string
     */
    public function getCostPriceAttribute($value)
    {
        $costPrice = 0;

        if ($this->attributes['composite']) {
            foreach ($this->compositeProducts as $k => $product) {
                
                $costPrice += (float)$product->simple->cost_price * $product->amount;
            }
        } else {
            
            $costPrice = $value;
        
        }

        return number_format((float)$costPrice, 2, '.', ''); // duas casas decimais
    }

    public function getRetailPriceAttribute($value)
    {
        return number_format((float)$value, 2, '.', ''); // duas casas decimais
    }


}
