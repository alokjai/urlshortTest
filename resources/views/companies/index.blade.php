@extends('layouts.app')

@section('title', 'Companies')

@section('content')
    <h1>Companies</h1>

    <a href="{{ route('companies.create') }}" class="btn">Create New Company</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Users Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td>{{ $company->id }}</td>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->users->count() }}</td>
                    <td>
                        <a href="{{ route('companies.show', $company) }}" class="btn">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
