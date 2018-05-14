@extends('layouts.noapp')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Lavachart Test</div>

        <div class="card-body">
          <div class="btn-group btn-group-lg">
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                {{ $curRoom->name }}
                @if (!empty($curRoom->alt_name))
                ({{ $curRoom->alt_name }})
                @endif
              </button>
              <div class="dropdown-menu">
                @foreach ($rooms as $room)
                <a class="dropdown-item" href="/test/{{ $room->internal_id }}">
                  {{ $room->name }}
                  @if (!empty($room->alt_name))
                  ({{ $room->alt_name }})
                  @endif
                </a>
                @endforeach
              </div>
            </div>
          </div>
          <div id="last_day_co2_div"></div>
          <div id="last_day_voc_div"></div>
          <div id="last_day_rest_div"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@linechart('LastDayCO2', 'last_day_co2_div')
@linechart('LastDayVOC', 'last_day_voc_div')
@linechart('LastDayRest', 'last_day_rest_div')
@endsection