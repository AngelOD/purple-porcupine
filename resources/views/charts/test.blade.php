@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Lavachart Test</div>

                <div class="card-body">
                    <div id="last_day_div"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@linechart('LastDay', 'last_day_div')
@endsection