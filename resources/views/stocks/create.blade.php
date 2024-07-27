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
            <h1>Adicionar produto no estoque</h1>
        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <hr>
    <form action="{{ route('stocks.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="product_id">Produto</label>
            <select class="form-control" id="product_id" name="product_id">
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Quantidade</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

@endsection