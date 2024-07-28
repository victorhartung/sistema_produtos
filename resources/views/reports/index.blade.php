@extends('layouts.app')
@section('content')
    
<div class="container">
    <h1>Relatórios</h1>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h2>Entrada de Estoque</h2>
            <form id="form-entry" action="{{ route('get_excel_entries') }}" method="GET">
                <div class="form-group">
                    <label for="start_date_entrada">Data Inicial</label>
                    <input type="date" class="form-control" id="start_date_entrada" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date_entrada">Data Final</label>
                    <input type="date" class="form-control" id="end_date_entrada" name="end_date" required>
                </div>
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </form>
        </div>
        <div class="col-md-6">
            <h2>Saída de Estoque</h2>
            <form action="{{ route('get_excel_exits') }}" method="GET">
                <div class="form-group">
                    <label for="start_date_saida">Data Inicial</label>
                    <input type="date" class="form-control" id="start_date_saida" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date_saida">Data Final</label>
                    <input type="date" class="form-control" id="end_date_saida" name="end_date" required>
                </div>
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </form>
        </div>
    </div>
</div>

@endsection
