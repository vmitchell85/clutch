@extends('layouts.bootstrap')

@section('body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <dashboard :filter="filter" :counts="counts"></dashboard>
            </div>
        </div>
    </div>
@stop