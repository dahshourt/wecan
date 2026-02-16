@forelse ($collection as $item)
    <tr>
        <td>
            <a href='{{ url("$route") }}/{{ $item->id }}/edit' class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">{{ $item->id }}</a>
        </td>
        <td>
            <a href='{{ url("$route") }}/{{ $item->id }}/edit' class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">{{ $item->name }}</a>
        </td>
        <td>
            <span class="text-dark-75 d-block font-size-lg">{{ $item->vendor->name ?? 'N/A' }}</span>
        </td>
        <td>
            <span class="label label-lg label-light-primary label-inline font-weight-bold py-4">{{ $item->status->name ?? 'N/A' }}</span>
        </td>
        <td>
            <span class="text-dark-75 d-block font-size-lg">{{ $item->go_live_planned_date ?? 'N/A' }}</span>
        </td>
        <td>
            <span class="text-dark-75 d-block font-size-lg">{{ $item->release_start_date ?? 'N/A' }}</span>
        </td>
        <td class="text-right">
            @can('Show Release')
                <a href='{{ url("$route") }}/{{ $item->id }}' class="btn btn-icon btn-light btn-hover-primary btn-sm mr-2"
                   title="View Details">
                    <span class="svg-icon svg-icon-md svg-icon-primary">
                        <!--View Icon-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                             height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path
                                    d="M12,20 C7.581722,20 4,16.418278 4,12 C4,7.581722 7.581722,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 Z M12,6 C8.6862915,6 6,8.6862915 6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 Z"
                                    fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path
                                    d="M12,16 C14.209139,16 16,14.209139 16,12 C16,9.790861 14.209139,8 12,8 C9.790861,8 8,9.790861 8,12 C8,14.209139 9.790861,16 12,16 Z"
                                    fill="#000000" fill-rule="nonzero"/>
                            </g>
                        </svg>
                    </span>
                </a>
            @endcan
            @can('Edit Release')
                <a href='{{ url("$route") }}/{{ $item->id }}/edit' class="btn btn-icon btn-light btn-hover-primary btn-sm"
                   title="Edit">
                    <span class="svg-icon svg-icon-md svg-icon-primary">
                        <!--Edit Icon-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                             height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path
                                    d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                            </g>
                        </svg>
                    </span>
                </a>
            @endcan
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted font-weight-bold py-5">No Releases found.</td>
    </tr>
@endforelse
