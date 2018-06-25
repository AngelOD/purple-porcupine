@extends('layouts.app')

@section('content')
@if (session('status'))
  <div class="alert alert-success">
    {{ session('status') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif
<div class="container">
  <world-domination />
</div>
@endsection