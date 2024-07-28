<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; // Server side datatables
use Illuminate\Support\Facades\DB;

use App\User;
use App\Models\Product;
use App\Models\Stock;

class RequisitionController extends Controller
{
    public function index() {
        
        return view('requisitions.index');
    
    }

    public function create() {
        
        $users = User::all();
        $products = Product::all();
        
        return view('requisitions.create', compact('users', 'products'));
    
    }

    public function store(Request $request) {
        
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|integer|min:1',
            'requisition_date' => 'required|date',
            'is_exit' => 'required|boolean',
        ]);

        $todayDate = date('d-m-Y');
        
        if($request->requisition_date > $todayDate) {
            return redirect()->back()->withErrors(['error' => 'A data de requisição não pode ser maior que a atual']);
        }
        //dd($request->all());
        $amount = $request->amount;
        //dd($request->is_exit);
        
        $stock = Stock::where('product_id', $request->product_id)->first();
        
        //verifica se existe o produto requisitado cadastrado no estoque
        if(!$stock) {
                    
            return redirect()->back()->withErrors(['error' => 'Não há produtos cadastrados para esse tipo de requisição']);
        
        }

        if($request->is_exit) {
            //verifica se há quantidade disponível para produto simples
            $stock = Stock::where('product_id', $request->product_id)->first();
            
            //verifica se o produto é composto
            $compositeProducts = DB::table('composite_products')
            ->where('composite_id', $request->product_id)
            ->get();

            //se for composto ele verifica os componentes
            if($compositeProducts->isNotEmpty()) {
                
                foreach($compositeProducts as $component) {
                    
                    $componentStock = Stock::where('product_id', $component->simple_id)->first();
                    
                    if (!$componentStock || $componentStock->amount < $amount * $component->amount) {
                        
                        return redirect()->back()->withErrors(['error' => 'Estoque insuficiente para o componente do produto composto.']);
                    
                    }
                }
            }else {  
                //validação do produto simples
                if(!$stock || $stock->amount < $amount) {
                    
                    return redirect()->back()->withErrors(['error' => 'Estoque insuficiente para essa requisição de saída']);
                
                }
            }      
        }
       
        Requisition::create($request->all());
        
        return redirect()->route('requisitions.index')->with('success', 'Requisição feita com sucesso!');
    
    }

    public function show(Requisition $requisition) {
        
        return view('requisitions.show', compact('requisition'));
    
    }

    public function edit(Requisition $requisition) {
        
        $users = User::all();
        $products = Product::all();
        
        return view('requisitions.edit', compact('requisition', 'users', 'products'));
    
    }

    public function update(Request $request, Requisition $requisition) {
        
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|integer',
            'requisition_date' => 'required|date',
            'is_exit' => 'required|boolean',
        ]);

        $todayDate = date('d-m-Y');

        if($request->requisition_date > $todayDate) {
            
            return redirect()->back()->withErrors(['error' => 'A data de requisição não pode ser maior que a atual']);
        
        }

         //dd($request->all());
         $amount = $request->amount;
         //dd($request->is_exit);
 
         if($request->is_exit) {
             
             //verifica se há quantidade disponível para produto simples
             $stock = Stock::where('product_id', $request->product_id)->first();
             
             //verifica se o produto é composto
             $compositeProducts = DB::table('composite_products')
             ->where('composite_id', $request->product_id)
             ->get();
 
             //se for composto ele verifica os componentes
            if($compositeProducts->isNotEmpty()) {
                 
                foreach($compositeProducts as $component) {
                     
                    $componentStock = Stock::where('product_id', $component->simple_id)->first();
                    
                    if (!$componentStock || $componentStock->amount < $amount * $component->amount) {
                        
                        return redirect()->back()->withErrors(['error' => 'Estoque insuficiente para o componente do produto composto.']);
                    
                    }
                 }
            }else {  
                 //validação do produto simples
                 if(!$stock || $stock->amount < $amount) {
                     
                     return redirect()->back()->withErrors(['error' => 'Estoque insuficiente para essa requisição de saída']);
                 
                }
            }      
        }

        $requisition->update($request->all());
        
        return redirect()->route('requisitions.index')->with('success', 'Requisição atualizada com sucesso.');
    
    }

    public function destroy(Requisition $requisition) {
        
        $requisition->delete();
        
        return redirect()->route('requisitions.index')->with('success', 'Requisição excluida com sucesso.');
    
    }

    //Datatables serverside
    public function getData() {
        
        $requisitions = Requisition::with('user', 'product')->get();
        
        //dd($requisitions);

        return Datatables::of($requisitions) 
        ->addColumn('type', function ($requisition) {
            return $requisition->is_exit ? 'Saída' : 'Entrada';
        })
        // Botoes de acao da tabela de produtos
        ->addColumn('action', function ($requisition) {
            return '<form action="' . route('requisitions.destroy', $requisition->id) . '"
            class="d-flex align-items-end delete" method="POST">
            ' . csrf_field() . method_field('DELETE') . '
            <div class="btn-group ml-auto shadow-sm">
            
                <a href="' . route('requisitions.edit', $requisition->id) . '" class="btn btn-outline-info">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt"></i> Apagar
                </button>
            </div>
        </form>';
        })->make(true);
    }
}
