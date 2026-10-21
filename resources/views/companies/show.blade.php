@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
    <h1>{{ $company->name }}</h1>

    <a href="{{ route('companies.index') }}" class="btn" style="background-color: #6c757d;">Back to Companies</a>

    <h2 style="margin-top: 20px;">Users</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($company->users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->getRoleNames()->first() ?? 'No role' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
