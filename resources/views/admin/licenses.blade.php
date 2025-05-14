@extends('admin.layouts.app')

@section('content')
<h2>Licenses</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Key</th>
            <th>Type</th>
            <th>Status</th>
            <th>Expires At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($licenses as $license)
        <tr>
            <td>{{ $license->id }}</td>
            <td>{{ $license->user->email }}</td>
            <td>{{ $license->key }}</td>
            <td>{{ $license->type }}</td>
            <td>{{ $license->status }}</td>
            <td>{{ $license->expires_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection