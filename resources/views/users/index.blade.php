@extends('layouts.app')
@section('content')

<style>
    .dataTables_filter {
        float: right !important;
    }
</style>

<div class="container">
    @if (!empty(session('success')))
    <div class="alert alert-success shadow-sm" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    
    <div class="row">
        <div class="col">
            <h1>Lista de Usuários</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('users.create') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus"></i> 
                Novo Usuário
            </a>
        </div>
    </div>

    <hr>
    <table id="users_table" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Nível</th>
                <th class="col-2">Ações</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->level }}</td>
                <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form class="delete" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection

@push('js')

<script>
$(document).ready(function() {
    $('#users_table').DataTable({
        
        drawCallback: function(settings) {
                
            $('.delete').submit(function() {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Tem certeza que deseja continuar?',
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