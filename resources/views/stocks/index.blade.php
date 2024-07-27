@extends('layouts.app')
@section('content')

<style>
    .dataTables_filter {
        float: right !important;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>Estoque</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stocks.create') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus"></i> 
                Adicionar produto
            </a>
        </div>

        @if (!empty(session('success')))
        <div class="alert alert-success shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>
    <hr>
    <table class="table table-bordered table-striped" style="width:100%;margin:0px !important" id="stocks-table">
        <thead>
            <tr>
                <th>ID do Produto</th>
                <th>Produto</th>
                <th class="col-2">Quantidade</th>
                <th class="col-2">Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection

@push('js')

<script>
    $(function() {
        $('#stocks-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stocks.getData') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'product.name', name: 'product.name' },
                { data: 'amount', name: 'amount' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],

            language: {
                    "processing": "A processar...",
                    "lengthMenu": "Exibindo _MENU_ registros",
                    "zeroRecords": "Não foram encontrados resultados",
                    "info": "Mostrando _START_ até _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando de 0 até 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros no total)",
                    "search": "<b>Procurar:</b>",
                    "paginate": {
                        "first": "Primeiro",
                        "previous": "Anterior",
                        "next": "Seguinte",
                        "last": "Último"
                    }
                },
        });
    });
</script>
@endpush