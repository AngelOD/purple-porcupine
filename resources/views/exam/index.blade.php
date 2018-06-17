@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row mb-5">
    <h1>
      Super Secret Room Manipulator
      <small class="text-muted">Only meant for Total World Domination<sup>TM</sup></small>
    </h1>
  </div>

  <div class="card">
    <div class="card-body">
      <h2 class="card-title">Room Definitions</h2>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/rd?tp=1" class="btn btn-warning btn-block btn-lg">
            Good Room
          </a>
        </div>
      </div>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/rd?tp=2&tmp=1&hum=1" class="btn btn-warning btn-block btn-lg">
            Bad Room
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/rd?tp=2&tmp=2&hum=1" class="btn btn-warning btn-block btn-lg">
            Bad Room (Warm)
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/rd?tp=2&tmp=1&hum=2" class="btn btn-warning btn-block btn-lg">
            Bad Room (Moist)
          </a>
        </div>
      </div>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/rd?tp=2&tmp=1&hum=1" class="btn btn-warning btn-block btn-lg">
            Bad Room
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/rd?tp=2&tmp=2&hum=1" class="btn btn-warning btn-block btn-lg">
            Bad Room (Warm)
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/rd?tp=2&tmp=1&hum=2" class="btn btn-warning btn-block btn-lg">
            Bad Room (Moist)
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h2 class="card-title">Transitions</h2>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/fp?tp=1" class="btn btn-warning btn-block btn-lg">
            Good -&gt; Bad
          </a>
        </div>
      </div>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/fp?tp=2" class="btn btn-warning btn-block btn-lg">
            Good -&gt; Bad (Warm)
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/fp?tp=3" class="btn btn-warning btn-block btn-lg">
            Good -&gt; Bad (Humid)
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/fp?tp=4" class="btn btn-warning btn-block btn-lg">
            Good -&gt; Bad (Warm and Humid)
          </a>
        </div>
      </div>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/fp?tp=5" class="btn btn-danger btn-block btn-lg">
            Good -&gt; Horrible
          </a>
        </div>
      </div>
      <div class="row my-4">
        <div class="col-sm">
          <a href="/exam/fp?tp=6" class="btn btn-danger btn-block btn-lg">
            Good -&gt; Horrible (Hot)
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/fp?tp=7" class="btn btn-danger btn-block btn-lg">
            Good -&gt; Horrible (Wet)
          </a>
        </div>
        <div class="col-sm">
          <a href="/exam/fp?tp=8" class="btn btn-danger btn-block btn-lg">
            Good -&gt; Horrible (Hot and Wet)
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection