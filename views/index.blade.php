@php
    $admin = new \Wildfire\Core\Admin;
    $auth = new \Wildfire\Auth\Auth;
    $dash = new \Wildfire\Core\Dash;

    $currentUser = $auth->getCurrentUser();

    if ($_POST) {
        if ($_POST['row_id']) {
            $db_record = $dash->get_content($_POST['row_id']);
        } else if ($_POST['type'] && $_POST['slug']) {
            $search = array(
                'type' => $_POST['type'],
                'slug' => $_POST['slug']
            );
            $db_record = $dash->get_content($search);
        }
    }

    $json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT;
@endphp

@extends('templates.admin')

@section('body')
    {!! $admin->get_admin_menu('dash') !!}

    <div class="card-group m-0">
        @auth('admin')
            <div class="card my-2">
                <div class="card-header">Search</div>
                <div id="search_wrapper" class="card-body">
                    <nav>
                        <div class="nav nav-pills" id="nav-tab" role="tablist">
                            <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                                aria-controls="nav-home" aria-selected="true">By Id</a>
                            <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
                                aria-controls="nav-profile" aria-selected="false">User by slug</a>
                            <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab"
                                aria-controls="nav-contact" aria-selected="false">Type+Slug</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <form id="searchById" class="needs-validation bg-white" method="post" action="/admin" novalidate>
                                <div class="mb-3 input-group">
                                    <input type="number" name="row_id" class="form-control" placeholder="Search record by Id"
                                        required>
                                    <button class="btn btn-secondary" type="submit" data-search="id"><i
                                            class="far fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <form id="searchByUserSlug" class="bg-white" method="post" action="/admin" novalidate>
                                <div class="mb-3 input-group">
                                    <input type="hidden" name="type" value="user">
                                    <input type="text" name="slug" class="form-control" placeholder="Search user by slug"
                                        required>
                                    <button class="btn btn-secondary" type="submit" data-search="userSlug"><i
                                            class="far fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                            <form id="searchByType" class="bg-white" method="post" action="/admin" novalidate>
                                <div class="mb-3 input-group">
                                    <div class="input-group-prepend col-4 px-0">
                                        <select name="type" id="search_type" class="custom-select">
                                            <option value="" disabled selected hidden>Select Type</option>
                                            @foreach ($types as $t)
                                                @if ($t['type'] == 'content')
                                                    <option value="{{$t['slug']}}">{{ucfirst($t['plural'])}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="text" name="slug" class="form-control" placeholder="Search type by slug"
                                        required>
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit" data-search="typeSlug"><i
                                                class="far fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if ($_POST)
                        <div class="card mb-3">
                            <a class="w-100 text-left card-header d-flex justify-content-between align-items-center text-decoration-none" data-toggle="collapse" href="#search_output" role="button"
                                aria-expanded="false" aria-controls="search_output">
                                <span>
                                    {{$db_record['id']}} &#8594; {{$db_record['type']}} &#8594; {{$db_record['slug']}}
                                </span>
                                <i class="fas fa-plus-square"></i>
                            </a>
                            <div class="collapse" id="search_output">
                                <div class="card-body search_output">
                                    <pre>{{json_encode($db_record, $json_options)}}</pre>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <span class="col-6 border-right border-light">
                                            Created:<br>{{\date('d-M-Y H:i', $db_record['created_on'])}}
                                        </span>
                                        <span class="col-6">
                                            Updated:<br>{{\date('d-M-Y H:i', $db_record['updated_on'])}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endauth
        {{-- auth - admin --}}

        <div class="card my-2">
            <div class="card-header">Analysis</div>
            <div class="card-body">
                @if($_POST)
                    @php
                    $types_keys = array_keys($types);

                    foreach($db_record as $key => $value) {
                        if (in_array($key, $types_keys)) {
                            $search = [
                                'type' => $key,
                                'slug' => $value
                            ];
                            $db_record_dependency[] = $dash->get_content($search);
                        }
                    }

                    $sql = new \Wildfire\Core\MySQL;

                    $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}_id'='{$db_record['slug']}'");

                    if ($q) {
                        foreach($q as $v) {
                            $db_record_dependency = $dash->get_content($v['id']);
                        }
                        unset($q);
                    }

                    $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE FIND_IN_SET(`content`->>'$.{$db_record['type']}_ids', '{$db_record['id']}')");

                    if ($q) {
                        foreach($q as $v) {
                            $db_record_dependency = $dash->get_content($v['id']);
                        }
                        unset($q);
                    }

                    $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}'='{$db_record['slug']}'");

                    if ($q) {
                        foreach($q as $v) {
                            $db_record_dependency = $dash->get_content($v['id']);
                        }
                        unset($q);
                    }

                    $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE JSON_CONTAINS(`content`->>'$.{$db_record['type']}', '\"{$db_record['slug']}\"', '$')");

                    if ($q) {
                        foreach($q as $v) {
                            $db_record_dependency = $dash->get_content($v['id']);
                        }
                        unset($q);
                    }
                    @endphp
                    @foreach($db_record_dependency as $key => $record)
                    <div class="card mb-3">
                        <a class="w-100 text-left card-header d-flex justify-content-between align-items-center text-decoration-none" data-toggle="collapse" href="#output_{{$key}}" role="button"
                            aria-expanded="false" aria-controls="output_{{$key}}">
                            <span>{{$record['id']}} &#8594; {{$record['type']}} &#8594; {{$record['slug']}}</span>
                            <i class="fas fa-plus-square"></i>
                        </a>
                        <div class="collapse" id="output_{{$key}}">
                            <div class="card-body search_output">
                                <pre>{{json_encode($record, $json_options)}}</pre>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <span class="col-6 border-right border-light">
                                        Created:<br>{{\date('d-M-Y H:i', $record['created_on'])}}
                                    </span>
                                    <span class="col-6">
                                        Updated:<br>{{\date('d-M-Y H:i', $record['updated_on'])}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
