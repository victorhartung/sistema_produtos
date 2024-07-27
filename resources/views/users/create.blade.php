@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Adicionar Usuário</h2>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="level">Nível</label>
            <select class="form-control" id="level" name="level" required>
                <option value="user">Usuário</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form>
</div>

@endsection