<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; // Server side datatables

use App\Models\Product;
use App\Models\CompositeProduct;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        return view('products.index');
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        //dd($request->products);
        return view('products.create');
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validações
        $rules = [
            
            'name' => 'required',
            'cost_price' => 'required',
            'retail_price' => 'required',
            'products' => 'required_with:composite'

        ];

        $feedback = [
            
            'required' => 'O campo :attribute é obrigatório'
        
        ];

        $request->validate($rules, $feedback);

        //adiciona o produto simples

        $costPrice = str_replace(['.', ','], ['', '.'], $request->cost_price);
        $retailPrice = str_replace(['.', ','], ['', '.'], $request->retail_price);
        
        $product = new Product();   
        //valida se o preço de venda é menor que o preço de custo
        if($retailPrice < $costPrice) {
            
            return redirect()->back()->withErrors(['retail_price' => 'O preço de venda deve ser maior que o preço de custo']);
        
        }

        $product->name = $request->name;

        //Adiciona no banco o valor de custo do produto composto baseado na quantidade de produtos simples que ele tem
        if (empty($request->composite)){ 
            
            $product->cost_price = $costPrice; 
        
        }else {
            
            $totalCost = 0;
            
            foreach ($request->products as $simple_product_id => $amount) {
                
                $simpleProduct = Product::find($simple_product_id);
                
                $totalCost += $simpleProduct->cost_price * $amount;
            
            }
            
            $product->cost_price = $totalCost;
        
        }  

        $product->retail_price = $retailPrice;
        $product->composite = !empty($request->composite) ? true : false;
        $product->save();

        //adiciona o produto composto baseado nos produtos simples
        
        if (!empty($request->composite)) {
            foreach ($request->products as $simple_products => $amount) {
                $prod = new CompositeProduct();
                $prod->composite_id = $product->id;
                $prod->simple_id = $simple_products;
                $prod->amount = $amount; 
                $prod->save();
            }
        }

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product) {
        
        return view('products.edit', compact('product'));
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product) {
      
        $rules = [
            
            'name' => 'required',
            'cost_price' => 'required',
            'retail_price' => 'required',
            'products' => 'required_with:composite'

        ];

        $feedback = [
           
            'required' => 'O campo :attribute é obrigatório'
        
        ];

        $request->validate($rules, $feedback);

        // remove as virgulas e pontos da máscara
        $costPrice = str_replace(['.', ','], ['', '.'], $request->cost_price);
        $retailPrice = str_replace(['.', ','], ['', '.'], $request->retail_price);

        //valida se o preço de venda é menor que o preço de custo
        if($retailPrice < $costPrice) {
            
            return redirect()->back()->withErrors(['retail_price' => 'O preço de venda deve ser maior que o preço de custo']);
        
        }

        $product->name = $request->name;

         //Verifica se o produto é simples e adiciona o valor dele como simples se não, adiciona como produto composto
        if (empty($request->composite)){ 
            
            $product->cost_price = $costPrice ?? null; 
        
        }else {
            
            $totalCost = 0;
            
            foreach ($request->products as $simple_product_id => $amount) {
                
                $simpleProduct = Product::find($simple_product_id);
                
                $totalCost += $simpleProduct->cost_price * $amount;
            
            }
            
            $product->cost_price = $totalCost;
        
        }  

        $product->retail_price = $retailPrice;
        $product->composite = !empty($request->composite) ? true : false;
        $product->save();

        CompositeProduct::where('composite_id', $product->id)->delete();
        //se o produto for composto, atualiza como produto composto
        if (!empty($request->composite)) {
            
            foreach ($request->products as $simple_products => $amount) {
               
                $prod = new CompositeProduct();
                $prod->composite_id = $product->id;
                $prod->simple_id = $simple_products;
                $prod->amount = $amount;    
                $prod->save();
            
            }
        }

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        CompositeProduct::where('simple_id', $product->id)->delete();

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto apagado com sucesso!');
    }

    /**
     * Metodo de carregamento server side do datatable
     * @return array
     */
    public function dataTable()
    {

        $products = Product::select(['id', 'name', 'cost_price', 'retail_price', 'composite'])->get();
        //dd($products);
        return Datatables::of($products)
            // Traducao bool de composto
            ->addColumn('type', function ($product) {
                return $product->composite ? 'COMPOSTO' : 'SIMPLES';
            })
            // Botoes de acao da tabela de produtos
            ->addColumn('action', function ($product) {
                return '<form action="' . route('products.destroy', $product->id) . '"
                class="d-flex align-items-end delete" method="POST">
                ' . csrf_field() . method_field('DELETE') . '
                <div class="btn-group ml-auto shadow-sm">
                    <a href="' . route('products.edit', $product->id) . '" class="btn btn-outline-info">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt"></i> Apagar
                    </button>
                </div>
            </form>';
            })->make(true);
    }

    /**
     * Metodo para pesquisa de produtos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function search(Request $request)
    {
        if (!empty($request->id)) {
            // Dados para a tabela
            return Product::find($request->id);
        } else {
            // Consulta de produtos simples para fazer a barra de pesquisa
            $search = Product::where('composite', 0)->where('name', 'LIKE', '%' . $request->search . '%')->select('id', 'name as text');

            if(!empty($request->not)) $search->whereNotIn('id', $request->not);

            $search = $search->get();

            return ["results" => $search]; // Retorno na forma da biblioteca select2
        }
    }
}