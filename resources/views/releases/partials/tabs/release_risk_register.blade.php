{{-- Release Risk Register Tab Content --}}
<div class="mb-5">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="la la-exclamation-triangle text-warning mr-2"></i>Release Risk Register
            </h4>
            <p class="text-muted mb-0">Manage risks associated with this release.</p>
        </div>
        @php
            $risks = $row->risks ?? collect();
            $openRisks = $risks->where('risk_status_id', 1)->count();
            $highRisks = $risks->where('risk_score', '>', 15)->count();
        @endphp
        <div class="d-flex">
            <div class="mr-5 text-center">
                <span class="font-size-h1 font-weight-boldest text-primary">{{ $risks->count() }}</span>
                <span class="d-block text-muted font-size-sm">Total Risks</span>
            </div>
            <div class="mr-5 text-center">
                <span class="font-size-h1 font-weight-boldest text-warning">{{ $openRisks }}</span>
                <span class="d-block text-muted font-size-sm">Open</span>
            </div>
            <div class="text-center">
                <span class="font-size-h1 font-weight-boldest text-danger">{{ $highRisks }}</span>
                <span class="d-block text-muted font-size-sm">High Risk</span>
            </div>
        </div>
    </div>
</div>

{{-- Add Risk Form --}}
<div class="card card-custom card-border shadow-sm mb-8">
    <div class="card-header bg-light-primary py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-plus-circle text-primary mr-2"></i>Add New Risk
            </h3>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-icon btn-sm btn-light" data-toggle="collapse" data-target="#riskFormBody">
                <i class="la la-angle-down"></i>
            </button>
        </div>
    </div>
    <div class="card-body collapse show" id="riskFormBody">
        <form action="{{ route('releases.risks.store', $row->id) }}" method="POST" id="riskForm">
            @csrf
            
            {{-- Row 1: CR, Category, Status --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Relevant CR Number</label>
                        <select name="cr_id" class="form-control select2">
                            <option value="">-- Select CR (Optional) --</option>
                            @php
                                $changeRequests = $row->changeRequests ?? collect();
                            @endphp
                            @foreach($changeRequests as $cr)
                                <option value="{{ $cr->id }}">
                                    #{{ $cr->cr_no ?? $cr->id }} - {{ $cr->title ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Risk Category <span class="text-danger">*</span></label>
                        <select name="risk_category_id" class="form-control select2" required>
                            <option value="">-- Select Category --</option>
                            @foreach($riskCategories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="risk_status_id" class="form-control select2" required>
                            <option value="">-- Select Status --</option>
                            @foreach($riskStatuses ?? [] as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Row 2: Risk Description --}}
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Risk Description <span class="text-danger">*</span></label>
                        <textarea name="risk_description" class="form-control" rows="3" 
                                  placeholder="Describe the risk in detail..." required></textarea>
                    </div>
                </div>
            </div>

            {{-- Row 3: Impact, Probability, Score, Owner --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Impact Level <span class="text-danger">*</span></label>
                        <select name="impact_level" class="form-control" required id="impact_level">
                            <option value="">Select</option>
                            <option value="1">1 - Very Low</option>
                            <option value="2">2 - Low</option>
                            <option value="3">3 - Medium</option>
                            <option value="4">4 - High</option>
                            <option value="5">5 - Very High</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Probability <span class="text-danger">*</span></label>
                        <select name="probability" class="form-control" required id="probability">
                            <option value="">Select</option>
                            <option value="1">1 - Very Low</option>
                            <option value="2">2 - Low</option>
                            <option value="3">3 - Medium</option>
                            <option value="4">4 - High</option>
                            <option value="5">5 - Very High</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Risk Score</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-center font-weight-bold" id="risk_score_preview" 
                                   value="-" readonly style="background-color: #f3f6f9;">
                        </div>
                        <small class="text-muted">Auto-calculated</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Owner</label>
                        <select name="owner" class="form-control select2" id="risk_owner_select">
                            <option value="">-- Select Owner --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Row 4: Mitigation & Contingency Plans --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Mitigation Plan</label>
                        <textarea name="mitigation_plan" class="form-control" rows="3" 
                                  placeholder="Actions to reduce risk likelihood or impact..."></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Contingency Plan</label>
                        <textarea name="contingency_plan" class="form-control" rows="3" 
                                  placeholder="Actions if the risk occurs..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Row 5: Dates --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Date Identified</label>
                        <input type="date" name="date_identified" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Target Resolution Date</label>
                        <input type="date" name="target_resolution_date" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Row 6: Comment --}}
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Additional Comments</label>
                        <textarea name="comment" class="form-control" rows="2" 
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="text-right border-top pt-4">
                <button type="reset" class="btn btn-light font-weight-bold mr-2">
                    <i class="la la-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                    <i class="la la-plus"></i> Add Risk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Risk Register Table --}}
<div class="card card-custom shadow-sm">
    <div class="card-header bg-light py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-list-alt text-primary mr-2"></i>Registered Risks
            </h3>
        </div>
        <div class="card-toolbar">
            <span class="label label-light-primary label-lg font-weight-bold">
                {{ $risks->count() }} Records
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($risks->isEmpty())
            <div class="text-center py-10">
                <i class="la la-shield-alt text-muted" style="font-size: 64px;"></i>
                <p class="text-muted mt-4 mb-0">No risks registered yet. Use the form above to add risks.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center table-hover mb-0">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="pl-5" style="width: 90px;">Risk ID</th>
                            <th style="width: 70px;">CR#</th>
                            <th style="min-width: 200px;">Description</th>
                            <th style="width: 100px;">Category</th>
                            <th class="text-center" style="width: 70px;">Impact</th>
                            <th class="text-center" style="width: 70px;">Prob.</th>
                            <th class="text-center" style="width: 70px;">Score</th>
                            <th style="width: 90px;">Status</th>
                            <th style="width: 100px;">Owner</th>
                            <th style="width: 100px;">Identified</th>
                            <th class="text-center pr-5" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($risks as $risk)
                            <tr>
                                <td class="pl-5">
                                    <span class="font-weight-bold text-primary">{{ $risk->risk_id }}</span>
                                </td>
                                <td>
                                    @if($risk->cr_id)
                                        <a href="{{ route('show.cr', $risk->cr_id) }}" target="_blank" 
                                           class="text-dark-75 font-weight-bold">
                                            #{{ optional($risk->changeRequest)->cr_no ?? $risk->cr_id }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark-75 font-weight-bold" title="{{ $risk->risk_description }}">
                                        {{ Str::limit($risk->risk_description, 50) }}
                                    </div>
                                    @if($risk->mitigation_plan)
                                        <small class="text-muted d-block">
                                            <i class="la la-shield-alt"></i> {{ Str::limit($risk->mitigation_plan, 30) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="label label-light-info label-inline font-weight-bold">
                                        {{ optional($risk->category)->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold">{{ $risk->impact_level }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold">{{ $risk->probability }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $scoreColor = 'success';
                                        if ($risk->risk_score > 15) $scoreColor = 'danger';
                                        elseif ($risk->risk_score > 5) $scoreColor = 'warning';
                                    @endphp
                                    <span class="label label-{{ $scoreColor }} label-lg font-weight-bolder" style="min-width: 40px;">
                                        {{ $risk->risk_score }}
                                    </span>
                                </td>
                                <td>
                                    <span class="label label-light-{{ optional($risk->status)->color ?? 'secondary' }} label-inline font-weight-bold">
                                        {{ optional($risk->status)->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($risk->owner)
                                        <span class="text-dark-50" title="{{ $risk->owner }}">
                                            {{ $risk->owner }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($risk->date_identified)
                                        <span class="text-dark-50">{{ $risk->date_identified->format('d M Y') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center pr-5">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-icon btn-light-primary" 
                                                data-toggle="modal" data-target="#riskEditModal{{ $risk->id }}"
                                                title="Edit Risk">
                                            <i class="la la-pencil-alt"></i>
                                        </button>
                                        <form action="{{ route('releases.risks.destroy', [$row->id, $risk->id]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this risk?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Risk Edit Modal --}}
                            <div class="modal fade" id="riskEditModal{{ $risk->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('releases.risks.update', [$row->id, $risk->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-light-primary">
                                                <h5 class="modal-title font-weight-bold">
                                                    <i class="la la-pencil-alt text-primary mr-2"></i>
                                                    Edit Risk — {{ $risk->risk_id }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                {{-- Row 1: CR, Category, Status --}}
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Relevant CR Number</label>
                                                            <select name="cr_id" class="form-control select2-modal">
                                                                <option value="">-- Select CR (Optional) --</option>
                                                                @foreach($changeRequests as $cr)
                                                                    <option value="{{ $cr->id }}" {{ $risk->cr_id == $cr->id ? 'selected' : '' }}>
                                                                        #{{ $cr->cr_no ?? $cr->id }} - {{ $cr->title ?? 'N/A' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Risk Category <span class="text-danger">*</span></label>
                                                            <select name="risk_category_id" class="form-control select2-modal" required>
                                                                <option value="">-- Select Category --</option>
                                                                @foreach($riskCategories ?? [] as $category)
                                                                    <option value="{{ $category->id }}" {{ $risk->risk_category_id == $category->id ? 'selected' : '' }}>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                                            <select name="risk_status_id" class="form-control select2-modal" required>
                                                                <option value="">-- Select Status --</option>
                                                                @foreach($riskStatuses ?? [] as $status)
                                                                    <option value="{{ $status->id }}" {{ $risk->risk_status_id == $status->id ? 'selected' : '' }}>
                                                                        {{ $status->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 2: Risk Description --}}
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Risk Description <span class="text-danger">*</span></label>
                                                            <textarea name="risk_description" class="form-control" rows="3" required>{{ $risk->risk_description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 3: Impact, Probability, Score, Owner --}}
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Impact Level <span class="text-danger">*</span></label>
                                                            <select name="impact_level" class="form-control edit-impact" required>
                                                                <option value="">Select</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}" {{ $risk->impact_level == $i ? 'selected' : '' }}>
                                                                        {{ $i }} - {{ ['Very Low','Low','Medium','High','Very High'][$i-1] }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Probability <span class="text-danger">*</span></label>
                                                            <select name="probability" class="form-control edit-probability" required>
                                                                <option value="">Select</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}" {{ $risk->probability == $i ? 'selected' : '' }}>
                                                                        {{ $i }} - {{ ['Very Low','Low','Medium','High','Very High'][$i-1] }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Risk Score</label>
                                                            @php
                                                                $editScoreColor = '#C9F7F5'; $editTextColor = '#1BC5BD';
                                                                if ($risk->risk_score > 15) { $editScoreColor = '#FFE2E5'; $editTextColor = '#F64E60'; }
                                                                elseif ($risk->risk_score > 5) { $editScoreColor = '#FFF4DE'; $editTextColor = '#FFA800'; }
                                                            @endphp
                                                            <input type="text" class="form-control text-center font-weight-bold edit-score-preview" 
                                                                   value="{{ $risk->risk_score }}" readonly 
                                                                   style="background-color: {{ $editScoreColor }}; color: {{ $editTextColor }};">
                                                            <small class="text-muted">Auto-calculated</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Owner</label>
                                                            <select name="owner" class="form-control select2-modal">
                                                                <option value="">-- Select Owner --</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->name }}" {{ $risk->owner == $user->name ? 'selected' : '' }}>
                                                                        {{ $user->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 4: Mitigation & Contingency Plans --}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Mitigation Plan</label>
                                                            <textarea name="mitigation_plan" class="form-control" rows="3">{{ $risk->mitigation_plan }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Contingency Plan</label>
                                                            <textarea name="contingency_plan" class="form-control" rows="3">{{ $risk->contingency_plan }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 5: Dates --}}
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Date Identified</label>
                                                            <input type="date" name="date_identified" class="form-control" 
                                                                   value="{{ $risk->date_identified ? $risk->date_identified->format('Y-m-d') : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Target Resolution Date</label>
                                                            <input type="date" name="target_resolution_date" class="form-control" 
                                                                   value="{{ $risk->target_resolution_date ? $risk->target_resolution_date->format('Y-m-d') : '' }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 6: Comment --}}
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group mb-0">
                                                            <label class="font-weight-bold">Additional Comments</label>
                                                            <textarea name="comment" class="form-control" rows="2">{{ $risk->comment }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">
                                                    <i class="la la-times"></i> Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                                    <i class="la la-save"></i> Update Risk
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        // Calculate risk score for ADD form
        function updateRiskScore() {
            var impact = parseInt($('#impact_level').val()) || 0;
            var probability = parseInt($('#probability').val()) || 0;
            var score = impact * probability;
            
            if (score > 0) {
                $('#risk_score_preview').val(score);
                var bgColor = '#C9F7F5', textColor = '#1BC5BD';
                if (score > 15) { bgColor = '#FFE2E5'; textColor = '#F64E60'; }
                else if (score > 5) { bgColor = '#FFF4DE'; textColor = '#FFA800'; }
                $('#risk_score_preview').css({ 'background-color': bgColor, 'color': textColor });
            } else {
                $('#risk_score_preview').val('-').css({ 'background-color': '#f3f6f9', 'color': '#3F4254' });
            }
        }
        
        $('#impact_level, #probability').on('change', updateRiskScore);

        // Calculate risk score for EDIT modals
        $(document).on('change', '.edit-impact, .edit-probability', function() {
            var modal = $(this).closest('.modal');
            var impact = parseInt(modal.find('.edit-impact').val()) || 0;
            var probability = parseInt(modal.find('.edit-probability').val()) || 0;
            var score = impact * probability;
            var preview = modal.find('.edit-score-preview');
            
            if (score > 0) {
                preview.val(score);
                var bgColor = '#C9F7F5', textColor = '#1BC5BD';
                if (score > 15) { bgColor = '#FFE2E5'; textColor = '#F64E60'; }
                else if (score > 5) { bgColor = '#FFF4DE'; textColor = '#FFA800'; }
                preview.css({ 'background-color': bgColor, 'color': textColor });
            } else {
                preview.val('-').css({ 'background-color': '#f3f6f9', 'color': '#3F4254' });
            }
        });

        // Initialize Select2 inside modals
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('.select2-modal').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%'
                    });
                }
            });
        });
    });
</script>
@endpush
