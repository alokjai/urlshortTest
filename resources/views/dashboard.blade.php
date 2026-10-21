@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="user-info" style="background-color: #f4f4f4; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h1>Dashboard</h1>
        <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        <p><strong>Role:</strong> {{ auth()->user()->getRoleNames()->first() ?? 'No role' }}</p>
        <p><strong>Company:</strong> {{ auth()->user()->company_id ? auth()->user()->company->name ?? 'N/A' : 'N/A (SuperAdmin)' }}</p>
    </div>

    @if (auth()->user()->hasRole('SuperAdmin'))
        <h2>SuperAdmin Actions</h2>
        <ul>
            <li><a href="{{ route('companies.index') }}">View All Companies</a> - See all companies in the system</li>
            <li><a href="{{ route('companies.create') }}">Create New Company</a> - Add a new company to the system</li>
            <li><a href="{{ route('invitations.index') }}">View Invitations</a> - See all pending invitations</li>
            <li><a href="{{ route('invitations.create') }}">Send Invitation</a> - Invite new users (Admin/Member) to companies</li>
            <li><a href="{{ route('short-urls.index') }}">View All Short URLs</a> - See all short URLs from all companies</li>
        </ul>
    @elseif (auth()->user()->hasRole('Admin'))
        <h2>Admin Actions</h2>
        <ul>
            <li><a href="{{ route('invitations.index') }}">View Invitations</a> - See invitations for your company</li>
            <li><a href="{{ route('invitations.create') }}">Send Invitation</a> - Invite users (Admin/Member) to your company</li>
            <li><a href="{{ route('short-urls.index') }}">View Short URLs</a> - See short URLs created in your company</li>
            <li><a href="{{ route('short-urls.create') }}">Create Short URL</a> - Create new short URLs</li>
        </ul>
    @elseif (auth()->user()->hasRole('Member'))
        <h2>Member Actions</h2>
        <ul>
            <li><a href="{{ route('short-urls.index') }}">View Short URLs</a> - See short URLs created by you</li>
            <li><a href="{{ route('short-urls.create') }}">Create Short URL</a> - Create new short URLs</li>
        </ul>
    @endif
@endsection
