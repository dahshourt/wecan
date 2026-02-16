{{-- Stakeholder Register Tab Content --}}
<div class="mb-5">
    <h4 class="font-weight-bold text-dark mb-3">
        <i class="la la-user text-primary mr-2"></i>Stakeholder Register
    </h4>
    <p class="text-muted">Add stakeholders for Change Requests in this release.</p>
</div>

{{-- Add Stakeholder Form --}}
<div class="card card-custom card-border shadow-sm mb-5">
    <div class="card-header bg-light-primary py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-plus-circle text-success mr-2"></i>Add Stakeholder
            </h3>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-icon btn-sm btn-light" data-toggle="collapse" data-target="#stakeholderFormBody">
                <i class="la la-angle-down"></i>
            </button>
        </div>
    </div>
    <div class="card-body collapse" id="stakeholderFormBody">
        <form action="{{ route('releases.stakeholders.store', $row->id) }}" method="POST" id="stakeholderForm">
            @csrf
            <input type="hidden" name="release_id" value="{{ $row->id }}">
            
            <div class="row">
                {{-- CR Dropdown --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Change Request <span class="text-danger">*</span></label>
                        <select name="cr_id" class="form-control select2" id="stakeholder_cr_select" required>
                            <option value="">-- Select CR --</option>
                            @php
                                $changeRequests = $row->changeRequests ?? collect();
                            @endphp
                            @foreach($changeRequests as $cr)
                                <option value="{{ $cr->id }}">
                                    #{{ $cr->cr_no ?? $cr->id }} - {{ $cr->title ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @if($changeRequests->isEmpty())
                            <small class="text-warning">No CRs assigned to this release yet.</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- High Impact Stakeholder --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <span class="label label-danger label-dot mr-2"></span>High Impact Stakeholder
                        </label>
                        <select name="high_impact_stakeholder" class="form-control select2" id="stakeholder_high">
                            <option value="">-- Select User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Critical stakeholder with major impact</small>
                    </div>
                </div>

                {{-- Moderate Impact Stakeholder --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <span class="label label-warning label-dot mr-2"></span>Moderate Impact Stakeholder
                        </label>
                        <select name="moderate_impact_stakeholder" class="form-control select2" id="stakeholder_moderate">
                            <option value="">-- Select User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Stakeholder with moderate influence</small>
                    </div>
                </div>

                {{-- Low Impact Stakeholder --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <span class="label label-success label-dot mr-2"></span>Low Impact Stakeholder
                        </label>
                        <select name="low_impact_stakeholder" class="form-control select2" id="stakeholder_low">
                            <option value="">-- Select User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Stakeholder with minimal impact</small>
                    </div>
                </div>
            </div>

            <div class="text-right border-top pt-4">
                <button type="submit" class="btn btn-success font-weight-bolder px-8">
                    <i class="la la-plus"></i> Add Stakeholder
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Stakeholder Table --}}
<div class="card card-custom shadow-sm">
    <div class="card-header bg-light py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-list text-primary mr-2"></i>Registered Stakeholders
            </h3>
        </div>
        <div class="card-toolbar">
            @php $stakeholders = $row->stakeholders ?? collect(); @endphp
            <span class="label label-light-primary label-lg font-weight-bold">
                {{ $stakeholders->count() }} Records
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($stakeholders->isEmpty())
            <div class="text-center py-10">
                <i class="la la-users text-muted" style="font-size: 64px;"></i>
                <p class="text-muted mt-3 mb-0">No stakeholders registered yet. Use the form above to add stakeholders.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center table-hover mb-0">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="pl-5 text-center" style="width: 80px;">CR #</th>
                            <th>CR Name</th>
                            <th>Requester</th>
                            <th>
                                <span class="label label-danger label-dot mr-1"></span>High Impact
                            </th>
                            <th>
                                <span class="label label-warning label-dot mr-1"></span>Moderate Impact
                            </th>
                            <th>
                                <span class="label label-success label-dot mr-1"></span>Low Impact
                            </th>
                            <th class="text-center" style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stakeholders as $stakeholder)
                            <tr>
                                <td class="pl-5 text-center">
                                    <a href="{{ route('show.cr', $stakeholder->cr_id) }}" 
                                       target="_blank" class="font-weight-bold text-primary">
                                        #{{ optional($stakeholder->changeRequest)->cr_no ?? $stakeholder->cr_id }}
                                    </a>
                                </td>
                                <td>
                                    <span class="font-weight-bold">
                                        {{ optional($stakeholder->changeRequest)->title ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    {{ optional($stakeholder->changeRequest)->requester_name ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($stakeholder->high_impact_stakeholder)
                                        <span class="label label-light-danger label-inline font-weight-bold">
                                            {{ $stakeholder->high_impact_stakeholder }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stakeholder->moderate_impact_stakeholder)
                                        <span class="label label-light-warning label-inline font-weight-bold">
                                            {{ $stakeholder->moderate_impact_stakeholder }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stakeholder->low_impact_stakeholder)
                                        <span class="label label-light-success label-inline font-weight-bold">
                                            {{ $stakeholder->low_impact_stakeholder }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('releases.stakeholders.destroy', [$row->id, $stakeholder->id]) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this stakeholder?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                            <i class="la la-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
