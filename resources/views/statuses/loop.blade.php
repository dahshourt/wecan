@if($collection)

    @foreach ($collection as $item)
        <tr class="datatable-row" style="left: 0px; transition: background-color 0.3s ease;">
            <th scope="row" class="text-muted font-weight-bold align-middle">{{ $item->id }}</th>
            <td class="font-weight-bolder font-size-lg align-middle text-dark-75">{{ $item->name }}</td>
            <td class="align-middle">
                <span
                    class="label label-lg label-light-primary label-inline font-weight-bold py-4">{{ $item->stage->name }}</span>
            </td>
            <td class="align-middle">
                <span
                    class="label label-lg label-light-info label-inline font-weight-bold py-4">{{ $item->workflow_type?->name }}</span>
            </td>
            <td class="align-middle">
                @if($item->setByGroupStatuses && $item->setByGroupStatuses->isNotEmpty())
                    <div class="d-flex flex-column">
                        @foreach($item->setByGroupStatuses as $groupInfo)
                            <span class="text-muted font-weight-bold my-1"><i
                                    class="flaticon2-group mr-2 font-size-sm"></i>{{ $groupInfo?->group?->title }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted font-italic">N/A</span>
                @endif
            </td>
            <td class="align-middle">
                @if($item->viewByGroupStatuses && $item->viewByGroupStatuses->isNotEmpty())
                    <div class="d-flex flex-column">
                        @foreach($item->viewByGroupStatuses as $groupInfo)
                            <span class="text-muted font-weight-bold my-1"><i
                                    class="flaticon2-group mr-2 font-size-sm"></i>{{ $groupInfo?->group?->title }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted font-italic">N/A</span>
                @endif
            </td>
            @can('Active Status')
                <td class="align-middle">
                    @if($item->active)
                        <span class="switch switch-sm switch-icon switch-success _change_active" data-id="{{ $item->id }}"
                            style="cursor: pointer;">
                            <label>
                                <input type="checkbox" checked="checked" name="select" />
                                <span></span>
                            </label>
                        </span>
                    @else
                        <span class="switch switch-sm switch-icon switch-danger _change_active" data-id="{{ $item->id }}"
                            style="cursor: pointer;">
                            <label>
                                <input type="checkbox" name="select" />
                                <span></span>
                            </label>
                        </span>
                    @endif
                </td>
            @endcan
            @can('Edit Status')
                <td class="align-middle">
                    <a href='{{url("$route")}}/{{ $item->id }}/edit' class="btn btn-icon btn-light btn-hover-primary btn-sm"
                        title="Edit Status">
                        <span class="svg-icon svg-icon-md svg-icon-primary">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M12.2674799,18.2323597 L12.0084872,13.8896142 L18.6259343,7.27216704 C19.5751732,6.32292813 21.1141203,6.32292813 22.0633592,7.27216704 C23.0125981,8.22140595 23.0125981,9.76035306 22.0633592,10.709592 L15.445912,17.3270391 L11.1031665,17.0680464 L11.0849824,19.349696 L6.10427303,19.349696 C5.6267817,19.349696 5.2227183,18.9958742 5.16641777,18.5209706 L5.03926526,17.4485303 L5.03926526,17.4485303 L8.19692994,14.2908657 C8.23933583,14.6599725 8.39768567,15.0125514 8.67918374,15.2940494 C9.01899144,15.6338571 9.45828854,15.824687 9.92484735,15.824687 L12.2674799,18.2323597 Z"
                                        fill="#000000" fill-rule="nonzero"
                                        transform="translate(14.051939, 13.007842) rotate(-315.000000) translate(-14.051939, -13.007842) " />
                                    <path
                                        d="M7.05025253,16.5355339 L2.80761184,12.2928932 C2.41708756,11.9023689 2.41708756,11.2692039 2.80761184,10.8786797 L9.87867966,3.80761184 C10.2692039,3.41708756 10.9023689,3.41708756 11.2928932,3.80761184 L15.5355339,8.05025253 L7.05025253,16.5355339 Z"
                                        fill="#000000" opacity="0.3"
                                        transform="translate(9.171573, 9.979185) rotate(-315.000000) translate(-9.171573, -9.979185) " />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                    </a>
                </td>
            @endcan
        </tr>
    @endforeach
@else

    <tr>
        <td colspan="7" class="text-center text-muted font-weight-bold py-8">
            <i class="flaticon2-open-box icon-2x mr-2"></i> No Data Found
        </td>
    </tr>

@endif