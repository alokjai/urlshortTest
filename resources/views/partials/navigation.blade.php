<div class="navbar">
    @if (auth()->check())
        <a href="{{ route('dashboard') }}">Dashboard</a>

        @if (auth()->user()->hasRole('SuperAdmin'))
            <a href="{{ route('companies.index') }}">Companies</a>
            <a href="{{ route('companies.create') }}">Create Company</a>
            <a href="{{ route('invitations.index') }}">Invitations</a>
            <a href="{{ route('invitations.create') }}">Send Invitation</a>
            <a href="{{ route('short-urls.index') }}">Short URLs</a>
            <a href="{{ route('short-urls.create') }}">Create Short URL</a>
        @elseif (auth()->user()->hasRole('Admin'))
            <a href="{{ route('invitations.index') }}">Invitations</a>
            <a href="{{ route('invitations.create') }}">Send Invitation</a>
            <a href="{{ route('short-urls.index') }}">Short URLs</a>
            <a href="{{ route('short-urls.create') }}">Create Short URL</a>
        @elseif (auth()->user()->hasRole('Member'))
            <a href="{{ route('short-urls.index') }}">Short URLs</a>
            <a href="{{ route('short-urls.create') }}">Create Short URL</a>
        @endif

        <div class="navbar-right">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; padding: 14px 20px; cursor: pointer; font-size: 16px;">Logout</button>
            </form>
        </div>
    @else
        <a href="{{ route('login') }}">Login</a>
    @endif
</div>
