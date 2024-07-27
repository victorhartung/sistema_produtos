@extends('layouts.app')
@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="col-auto">
            <a href="{{ route('users.index') }}" class="btn btn-dark">
                <div class="fas fa-arrow-left"></div> 
                Voltar
            </a>
        </div>
        <div class="col">
            <h1 class="mb-0">Editar Usuário</h1>
        </div>
    </div>
    <hr>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="level">Nível</label>
            <select class="form-control" id="level" name="level" required>
                <option value="user" {{ $user->level == 'user' ? 'selected' : '' }}>Usuário</option>
                <option value="admin" {{ $user->level == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection