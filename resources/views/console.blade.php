@extends('layouts.console')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Console Dashboard</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <h5 class="card-title">Laravel Horizon</h5>
                              <p class="card-text">Horizon allows you to easily monitor key metrics of your queue system such as job throughput, runtime, and job failures.</p>
                              <a href="{{url('horizon')}}" class="btn btn-primary">Go</a>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <h5 class="card-title">Laravel Telescope</h5>
                              <p class="card-text">Telescope provides insight into the requests coming into your application, exceptions, log entries, database queries, queued jobs, mail, notifications, cache operations, scheduled tasks, variable dumps and more.</p>
                              <a href="{{url('telescope')}}" class="btn btn-primary">Go</a>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
