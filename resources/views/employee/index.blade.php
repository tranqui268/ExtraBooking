@extends('layouts.app')
@section('content')
<div class="container my-4">
    <h4 class="mb-4 text-center">Danh sách thợ sửa xe</h4>
    <div class="row" id="mechanic-list">
        
    </div>
</div>
@endsection
@section('scripts')
   @include('scripts.employee-scripts')
@endsection