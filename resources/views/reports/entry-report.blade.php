@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-auto">
            <a href="{{ route('reports.index') }}" class="btn btn-dark">
                <div class="fas fa-arrow-left"></div> 
                Voltar
            </a>
        </div>
        <div class="col">
            <h1>Relatório de entrada</h1>
        </div>
    </div>
    
    <hr>
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Nome do Produto</th>
                <th>Quantidade Total</th>
                <th>Preço de Custo Total(R$)</th>
                <th>Preço de Venda Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $data)
                <tr>   
                    <td>{{ $data->product_name }}</td>
                    <td>{{ $data->total_amount }}</td>
                    <td>{{ number_format($data->total_cost_price, 2) }}</td>
                    <td>{{ number_format($data->total_retail_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection