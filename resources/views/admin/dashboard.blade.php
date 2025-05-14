@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h3>{{ $stats['totalUsers'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Websites</h5>
                    <h3>{{ $stats['totalWebsites'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Published Articles</h5>
                    <h3>{{ $stats['publishedArticles'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Active Licenses</h5>
                    <h3>{{ $stats['activeLicenses'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
