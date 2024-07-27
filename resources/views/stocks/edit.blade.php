@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-auto">
            <a href="{{ route('stocks.index') }}" class="btn btn-dark">
                <div class="fas fa-arrow-left"></div> 
                Voltar
            </a>
        </div>
        <div class="col">
            <h1>Editar produto no estoque</h1>
        </div>
    </div>
    <hr>
    <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="product_id">Produto</label>
            <select class="form-control" id="product_id" name="product_id">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $product->id == $stock->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Quantidade</label>
            <input type="number" class="form-control" id="amount" name="amount" value="{{ $stock->amount }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Editar</button>
    </form>
</div>
@endsection