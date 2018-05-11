@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                Add Room
            </div>
            <div class="card-body">
                <p class="lead">Fill out the form to add a new room</p>
            <form class="form-horizontal" method="POST" action="{{ action('RoomController@store') }}">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="room-name">Name of room</label>
                    <div class="col-sm-6">
                        <input name="room-name" type="text" class="form-control" id="room-name" placeholder="0.1.95" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="room-name-alt">Alternative name for room</label>
                    <div class="col-sm-6">
                        <input name="room-name-alt" type="text" class="form-control" id="room-name-alt" placeholder="Auditorium" required>
                    </div>
                </div>
                <div class="form-group row-center">
                    <button name="add-room-btn" type="submit" class="btn btn-primary">Add Room</button>
                </div>
                <div class="form-group row-center">
                    @if (session('status'))
                <label class="col-form-label">{{session('status')}}</label>
                    @endif
                </div>
                {{ csrf_field() }}
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection
