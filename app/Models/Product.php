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

   
    // Relacionamento 1 pra muitos com requisição
    public function requisitions(){
        
        return $this->hasMany(Requisition::class);
    
    }
    
    // Relacionamento 1 pra muitos com estoque
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
     * Mutator para cálculo de custo dos produtos compostos baseado nos produtos simples
     *
     * @return string
     */
    public function getCostPriceAttribute($value) {
        
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

    public function getRetailPriceAttribute($value) {
        
        return number_format((float)$value, 2, '.', ''); // duas casas decimais
    
    }

     // Verifica se há registros relacionados na tabela requisicão ou estoque se houver registro, impede a exclusão
    protected static function boot() {
        
        parent::boot();

        static::deleting(function ($product) {
           
            if ($product->requisitions()->exists() || $product->stocks()->exists()) {

                throw new \Exception('Produto não pode ser excluído pois está referenciado em requisições ou estoque.');
            
            }
        });
    }


}
