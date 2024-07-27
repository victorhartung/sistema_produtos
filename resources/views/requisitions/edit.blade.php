@extends('layouts.app')
@section('content')

<div class="container">
    <h1>Editar Requisição</h1>
    <form action="{{ route('requisitions.update', $requisition->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="user_id">Usuário</label>
            <select class="form-control" id="user_id" name="user_id">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == $requisition->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="product_id">Produto</label>
            <select class="form-control" id="product_id" name="product_id">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $product->id == $requisition->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Quantidade</label>
            <input type="number" class="form-control" id="amount" name="amount" value="{{ $requisition->amount }}" required>
        </div>
        <div class="form-group">
            <label for="requisition_date">Data de requisição</label>
            <input type="datetime-local" class="form-control" id="requisition_date" name="requisition_date" value="{{ $requisition->requisition_date->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="form-group">
            <label for="is_exit">Tipo</label>
            <select class="form-control" id="is_exit" name="is_exit">
                <option value="0" {{ !$requisition->is_exit ? 'selected' : '' }}>Entrada</option>
                <option value="1" {{ $requisition->is_exit ? 'selected' : '' }}>Saída</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Editar requisição</button>
    </form>
</div>
@endsection