@extends('layouts.app')

@section('title', 'Invitations')

@section('content')
    <h1>Invitations</h1>

    <a href="{{ route('invitations.create') }}" class="btn">Send Invitation</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Company</th>
                <th>Status</th>
                <th>Token</th>
                <th>Invited By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invitations as $invitation)
                <tr>
                    <td>{{ $invitation->id }}</td>
                    <td>{{ $invitation->email }}</td>
                    <td>{{ $invitation->role }}</td>
                    <td>{{ $invitation->company->name ?? 'N/A' }}</td>
                    <td>{{ $invitation->status }}</td>
                    <td>
                        @if ($invitation->status === 'pending')
                            <code style="background-color: #f4f4f4; padding: 5px; font-size: 12px;">{{ $invitation->token }}</code>
                        @else
                            <span style="color: #6c757d;">-</span>
                        @endif
                    </td>
                    <td>{{ $invitation->invitedBy->name }}</td>
                    <td>
                        @if ($invitation->status === 'pending')
                            <form method="POST" action="{{ route('invitations.destroy', $invitation->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background-color: #dc3545;">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
