@extends('layouts.app')

@section('title', 'Create Short URL')

@section('content')
    <h1>Create Short URL</h1>

    <form method="POST" action="{{ route('short-urls.store') }}">
        @csrf

        <label>Original URL</label>
        <input type="url" name="original_url" required placeholder="https://example.com">

        <br><br>

        <button type="submit" class="btn">Create Short URL</button>
        <a href="{{ route('short-urls.index') }}" class="btn" style="background-color: #6c757d;">Cancel</a>
    </form>
@endsection
