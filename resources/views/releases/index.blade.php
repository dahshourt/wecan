@extends('layouts.app')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Release List
                                <span class="d-block text-muted pt-2 font-size-sm">Manage your Releases</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            @can('Create Release')
                                <a href='{{ url("$route/create") }}' class="btn btn-primary font-weight-bolder">
                                    <i class="la la-plus"></i> New Release
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center" id="releaseTable">
                                <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 50px">Release #</th>
                                    <th style="min-width: 200px">Release Name</th>
                                    <th style="min-width: 150px">Vendor Name</th>
                                    <th style="min-width: 100px">Status</th>
                                    <th style="min-width: 120px">Go Live Date</th>
                                    <th style="min-width: 120px">Start Date</th>
                                    <th class="text-right" style="min-width: 100px">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @include("$view.loop")
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $collection->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
