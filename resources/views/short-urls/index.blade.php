@extends('layouts.app')

@section('title', 'Short URLs')

@section('content')
    <h1>Short URLs</h1>

    @if (!auth()->user()->hasRole('SuperAdmin'))
        <a href="{{ route('short-urls.create') }}" class="btn">Create Short URL</a>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Short Code</th>
                <th>Original URL</th>
                <th>Company</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shortUrls as $shortUrl)
                <tr>
                    <td>{{ $shortUrl->id }}</td>
                    <td>{{ $shortUrl->short_code }}</td>
                    <td>{{ $shortUrl->original_url }}</td>
                    <td>{{ $shortUrl->company->name ?? 'N/A' }}</td>
                    <td>{{ $shortUrl->user->name }}</td>
                    <td>{{ $shortUrl->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <a href="{{ route('short-urls.redirect', $shortUrl->short_code) }}" target="_blank" class="btn">Test Redirect</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
