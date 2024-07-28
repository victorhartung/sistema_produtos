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
                <h1>Produtos</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('products.create') }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-plus"></i> 
                    Novo produto
                </a>
            </div>
        </div>
        <hr>
        @if (!empty(session('success')))
            <div class="alert alert-success shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

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
            <div class="col-12">
                <table class="table table-striped table-hover table-bordered" id="products" style="width:100%;margin:0px !important">
                    <thead>
                        <tr>
                            <th class="col-5">Nome</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Preco de custo (R$)</th>
                            <th class="text-center">Preco de venda (R$)</th>
                            <th class="col-2"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            var table = $('#products').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products.table') }}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'type', searchable: false},
                    {data: 'cost_price'},
                    {data: 'retail_price'},
                    {data: 'action', orderable: false, searchable: false},
                ],
                drawCallback: function(settings) {
                    $('tr').find('td:eq(1)').addClass('text-center')
                    $('tr').find('td:eq(2),td:eq(3)').mask("#.##0,00", {
                        reverse: true
                    }).addClass('text-right')

                    //confirmação de exclusão com a biblioteca sweetalert
                    $('.delete').submit(function() {
                        Swal.fire({
                            title: 'Atenção!',
                            text: 'Excluir o produto também o removerá de todos os produtos compostos, deseja continuar?',
                            icon: 'warning',
                            confirm: true,
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Excluir',
                            cancelButtonText: 'Cancelar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $(this).unbind().submit()
                            }
                        })
                        return false
                    })
                },

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