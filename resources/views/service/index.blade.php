@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Danh sách dịch vụ</h3>
    <div class="row" id="service-list">
        
    </div>
</div>
@endsection
@section('scripts')
    @include('scripts.service-scripts')
@endsection