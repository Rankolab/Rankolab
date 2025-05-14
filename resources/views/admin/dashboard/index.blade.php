@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Admin Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Overview of Platform Statistics</li>
    </ol>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h2>{{ $stats['totalUsers'] }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="{{ route('admin.users.index') }}">View all Users</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5>Websites</h5>
                    <h2>{{ $stats['totalWebsites'] }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="{{ route('admin.websites.index') }}">View all Websites</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5>Published Articles</h5>
                    <h2>{{ $stats['publishedArticles'] }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="{{ route('admin.articles.index') }}">View all Articles</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h5>Active Licenses</h5>
                    <h2>{{ $stats['activeLicenses'] }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="text-white stretched-link" href="{{ route('admin.licenses.index') }}">View all Licenses</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Optionally, add recent users or articles lists below -->

</div>
@endsection
