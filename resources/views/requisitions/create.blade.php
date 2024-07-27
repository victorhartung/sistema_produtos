@extends('layouts.app')
@section('content')

<div class="container">
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
    <div class="row">
        <div class="col-auto">
            <a href="{{ route('requisitions.index') }}" class="btn btn-dark">
                <div class="fas fa-arrow-left"></div> 
                Voltar
            </a>
        </div>
        <div class="col">
            <h1>Nova Requisição</h1>
        </div>
    </div>
    <hr>
    <form action="{{ route('requisitions.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="user_id">Usuário</label>
            <select class="form-control" id="user_id" name="user_id">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
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
        <div class="form-group">
            <label for="requisition_date">Data da Requisição</label>
            <input type="datetime-local" class="form-control" id="requisition_date" name="requisition_date" required>
        </div>
        <div class="form-group">
            <label for="is_exit">Tipo de requisição</label>
            <select class="form-control" id="is_exit" name="is_exit">
                <option value="0">Entrada</option>
                <option value="1">Saída</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Requisitar</button>
    </form>
</div>

@endsection