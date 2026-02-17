@extends('layouts.app')

@section('content')
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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Update CR Testable Flag</h2>
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
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    

                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <h3 class="card-title">Set CR Testable Flag</h3>
                        </div>
                        <!--begin::Form-->
                        <form class="form" action="{{ route('update_testable') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>CR Number <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="cr_number" 
                                           class="form-control form-control-solid @error('cr_number') is-invalid @enderror" 
                                           placeholder="Enter CR Number" 
                                           value="{{ old('cr_number') }}"
                                           required
                                           autofocus>
                                    @error('cr_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="checkbox-inline">
                                        <label class="checkbox checkbox-lg">
                                            <input type="hidden" name="testable" value="0">
                                            <input type="checkbox" 
                                                   name="testable" 
                                                   value="1" 
                                                   {{ old('testable') ? 'checked' : '' }}>
                                            <span></span>
                                            CR is Testable
                                        </label>
                                    </div>
                                    <span class="form-text text-muted">Check this box to mark the CR as testable (Don't check to mark the CR as not testable)</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">Update Status</button>
                                <a href="{{ url(path: '/') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection