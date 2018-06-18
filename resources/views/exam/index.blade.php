@extends('layouts.app')

@section('content')
@if (session('status'))
  <div class="alert alert-success">
    {{ session('status') }}
  </div>
@endif
<div class="container">
  <world-domination />
</div>
@endsection