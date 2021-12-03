@php
    $admin = new \Wildfire\Core\Admin;
    $dash = new \Wildfire\Core\Dash;

    $types = $dash->getTypes();
    $type = $_GET['type'];
    $currentRole = $this->getCurrentRole();
@endphp

@extends('templates.admin')

@section('body')
    <div class="p-3">
        {!! $admin->get_admin_menu('list', $type, $currentRole) !!}

        <h2 class="mb-4">
            @if ($type == 'user')
                {{ ucfirst($_GET['role']) }}
                <small><i class="fas fa-angle-double-right"></i></small>
            @endif

            List of {{ $types[$type]['plural'] }}
        </h2>

        <table class="my-4 table table-borderless table-hover datatable" data-jsonpath="list-json" data-type="{{ $type }}" data-role="{{ $currentRole }}">
            <thead class="thead-black">
                <tr>
                    <th scope="col">#</th>
                    @php
                        $displayed_field_slugs = array();
                    @endphp
                    @foreach ($types[$type]['modules'] as $i => $module)
                        @if (!in_array($module['input_slug'], $displayed_field_slugs))
                            @if (isset($module['list_field']) && $module['list_field'])
                                <th
                                    scope="col"
                                    class="pl-2"
                                    data-orderable="{{isset($module['list_sortable']) ? $module['list_sortable'] : 'false'}}"
                                    data-searchable="{{isset($module['list_searchable']) ? $module['list_searchable'] : 'false'}}"
                                    style="{{(isset($module['input_primary']) && $module['input_primary']) ? 'max-width:50%' : ''}}"
                                    >{{$module['input_slug']}}
                                </th>
                            @endif
                            @php
                                $displayed_field_slugs[] = $module['input_slug'];
                            @endphp
                        @endif
                    @endforeach
                    <th scope="col" data-orderable="false" data-searchable="false"></th>
                </tr>
            </thead>
        </table>
    </div>
@endsection
