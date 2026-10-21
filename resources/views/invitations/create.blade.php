@extends('layouts.app')

@section('title', 'Send Invitation')

@section('content')
    <h1>Send Invitation</h1>

    <form method="POST" action="{{ route('invitations.store') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Role</label>
        <select name="role" required>
            @foreach ($availableRoles as $role)
                <option value="{{ $role }}">{{ $role }}</option>
            @endforeach
        </select>

        @if (auth()->user()->hasRole('SuperAdmin'))
            <label>Company (Optional)</label>
            <select name="company_id">
                <option value="">Create New Company</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        @endif

        <br><br>

        <button type="submit" class="btn">Send Invitation</button>
        <a href="{{ route('invitations.index') }}" class="btn" style="background-color: #6c757d;">Cancel</a>
    </form>
@endsection
