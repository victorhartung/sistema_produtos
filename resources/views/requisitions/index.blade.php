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
            <h1>Requisições</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('requisitions.create') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus"></i> 
                Nova Requisição
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
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover table-bordered" id="requisition_table" style="width:100%;margin:0px !important">
                <thead>
                    <tr>
                        <th class="col-5">ID da Requisição</th>
                        <th class="text-center">Usuário requisitante</th>
                        <th class="text-center">Tipo de requisição</th>
                        <th class="text-center">Produto</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-center">Data requisitada</th>
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
        var table = $('#requisition_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                        url: "{{ route('requisitions.getData') }}",
                        //debugger
                        dataSrc:function(json) {
                            console.log(json);
                            return json.data
                        }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user.name', name: 'user.name' },
                    { data: 'type'},
                    { data: 'product.name', name: 'product.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'requisition_date', name: 'requisition_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                
                drawCallback: function(settings) {

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