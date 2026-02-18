@extends('layouts.app')
@section('content')

@include('change_request.partials.styles')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex flex-column">
                    <!--begin::Title-->
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                    <!--end::Title-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b example example-compact">
                        
                        <!--begin::Form-->
                            @php
                            foreach ($cr->change_request_custom_fields as $key => $value) {
                                if($value->custom_field_name == "testable")
                                {
                                $testable = $value->custom_field_value;
                                }
                            }
                            
                            @endphp
                        <form class="form" action='{{url("$route")}}/{{ $cr->id }}' method="post" enctype="multipart/form-data">

                            {{ csrf_field() }}
                            {{ method_field('PATCH') }}

                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title m-0 text-info">{{ $form_title.' #'.$cr->cr_no.' | '.($cr?->workflowType?->name ?: ' ') }}</h3>
                                <div class="d-flex">
                                    
                                    @can('Show CR Logs')
                                    <button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
                                    @endcan	
                                
                                </div>
                            </div>

                            
                            <input type="hidden" name="testable_flag" value="@if(!empty($testable)){{$testable}}@else{{0}}@endif" />
                            <input type="hidden" name="workflow_type_id" value="{{$workflow_type_id}}">
                            <input type="hidden" name="old_status_id" value="{{$cr->current_status->new_status_id}}">
                            <input type="hidden" name="cab_cr_flag" value="{{isset($cab_cr_flag)?$cab_cr_flag:0}}">
                            @if(request()->reference_status)
                                <input type="hidden" name="reference_status" value="{{ request()->reference_status }}">
                            @endif	
                            
                            <div class="card-body">
                                
                                <div class="row">
                                    @include("$view.custom_fields")
                                </div>
                                @if($cr->current_status->new_status_id == 113)
                                    @if(count($man_day) > 0)
                                        @php
                                            $manDayText = '';
                                            foreach ($man_day as $item) {
                                                $manDayText .= $item['custom_field_value'] . ' ';
                                            }
                                            $manDayText = trim($manDayText);
                                        @endphp

                                        <p><label class="form-control-lg">MD's</label> => {{ $manDayText }}</p>
                                    @endif
                                @endif
                                
                            </div>

                            <div class="card-footer d-flex justify-content-end">
                                @if(count($cr->set_status) > 0)
                                    @if($cr->getCurrentStatus()?->status?->id == 68 && $workflow_type_id == 9 && count($reminder_promo_tech_teams) > 0)
                                   
                                    <button type="submit" id="submit_button" class="btn btn-success">
                                            Submit
                                        </button>
                                    @else
                                        <button type="submit" id="submit_button" class="btn btn-success">
                                            Submit
                                        </button>
                                    @endif
                                @endif
                            </div>
                            
                            
                        </form>
                        <!--end::Form-->

                        <!-- start feedback table -->
                        @include('change_request.partials.feedback_section')
                        <!-- end feedback table -->

                        @include('change_request.partials.attachments_section')
                        
                    </div>
                    
                    @include('change_request.partials.defects_section')

                    @include("$view.cr_logs")
                    
                </div>
                
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->


@include('change_request.partials.scripts')

@endsection
