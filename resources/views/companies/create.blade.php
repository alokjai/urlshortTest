@extends('layouts.app')

@section('title', 'Create Company')

@section('content')
    <h1>Create Company</h1>

    <form method="POST" action="{{ route('companies.store') }}">
        @csrf

        <label>Company Name</label>
        <input type="text" name="name" required>

        <br><br>

        <button type="submit" class="btn">Create</button>
        <a href="{{ route('companies.index') }}" class="btn" style="background-color: #6c757d;">Cancel</a>
    </form>
@endsection
