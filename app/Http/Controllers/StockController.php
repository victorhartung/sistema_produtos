<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Yajra\Datatables\Datatables;
use App\Models\Stock;

class StockController extends Controller
{
    public function index() {
        
        return view('stocks.index');
    
    }

    public function create() {
        // Traz apenas os produtos simples
        $products = Product::where('composite', 0)->get();
        return view('stocks.create', compact('products'));
    
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|integer',
            'product_id' => function($attribute, $value, $fail) use ($request) {
                $exists = Stock::where('product_id', $request->product_id)->exists();
                if($exists) {
                    $fail('O produto já existe no estoque');
                }
            }
        ]);

        Stock::create($request->all());
        return redirect()->route('stocks.index')->with('success', 'Produto adicionado no estoque com sucesso.');
    }

    public function show(Stock $stock) {
        
        return view('stocks.show', compact('stock'));
    
    }

    public function edit(Stock $stock) {
        
        $products = Product::all();
        return view('stocks.edit', compact('stock', 'products'));
    
    }

    public function update(Request $request, Stock $stock) {
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|integer',
            'product_id' => function($attribute, $value, $fail) use ($request) {
                $exists = Stock::where('product_id', $request->product_id)->exists();
                if($exists) {
                    $fail('O produto já existe no estoque');
                }
            }
        ]);

        $stock->update($request->all());
        return redirect()->route('stocks.index')->with('success', 'Estoque atualizado com sucesso.');
    }

    public function destroy(Stock $stock) {
       
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Item excluído com sucesso.');
    
    }

    public function getData() {
        
        $stocks = Stock::with('product')->get();
            return Datatables::of($stocks) ->addColumn('action', function ($stock) {
                return '<form action="' . route('stocks.destroy', $stock->id) . '"
                class="d-flex align-items-end delete" method="POST">
                ' . csrf_field() . method_field('DELETE') . '
                <div class="btn-group ml-auto shadow-sm">
                    <a href="' . route('stocks.edit', $stock->id) . '" class="btn btn-outline-info">
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
