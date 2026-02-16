@extends('layouts.app')

@section('content')

<div class="container" id="results">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>CRs Crossed SLA</h3>

                    <a href="{{ route('report.cross_sla.export') }}" class="btn btn-success">
                        Export CRsCrossedSLA
                    </a>
                </div>
                <div class="card-header">
                     <form action="{{ route('reports.cr_crossed_sla') }}" method="GET">
                        <div class="row align-items-center">
                            
                             <div class="form-group col-md-3">
                                <label for="ticket_type">CR Type</label>
                                <select class="form-control" id="ticket_type" name="ticket_type">
                                    <option value="">Select....</option>
                                    <option value="1">Normal</option>
                                    <option value="3">Relevant with</option>
                                    <option value="2">Depend On</option>
                                </select>
                            </div>
                            
                            <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="top_management" id="top_management" value="1" {{ request('top_management') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="top_management">Top Management</label>
                            </div>
                             <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="on_hold" id="on_hold" value="1" {{ request('on_hold') ? 'checked' : '' }}>
                                <label class="form-check-label" for="on_hold">ON-Hold</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="on_behalf" id="on_behalf" value="1" {{ request('on_behalf') ? 'checked' : '' }}>
                                <label class="form-check-label" for="on_behalf">On Behalf</label>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                         
                    </form>
                </div>
                <div class="card-body">
                    @if($results->count())
                        <!-- Responsive table wrapper -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        @foreach(array_keys((array)$results->first()) as $column)
                                            <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $row)
                                        <tr>
                                            @foreach((array)$row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $results->links() }}
                        </div>
                    @else
                        <p>No results found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('css')
<style>
    /* Prevent table cells from wrapping and allow horizontal scroll */
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        white-space: nowrap;
    }
    
    /* Larger checkboxes */
    .form-check-input {
        width: 1.5em;
        height: 1.5em;
        margin-top: 0.15em;
        cursor: pointer;
    }
    
    .form-check-label {
        font-size: 1.1em;
        margin-left: 0.5em;
        cursor: pointer;
    }
    
    .form-check-inline {
        margin-right: 1.5em;
    }
</style>
@endpush
@push('css')
<style>
    /* Prevent table cells from wrapping and allow horizontal scroll */
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        white-space: nowrap;
    }
</style>
@endpush
