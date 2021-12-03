@php
    require_once __DIR__.'/../init.php';

    $lang = $types['webapp']['lang'] ?: 'en';
    $app_title = $types['webapp']['headmeta_title'] ?? 'Sachi';
    $app_title = $app_title ? "&raquo; $app_title" : '';

    if ($menus['admin_menu'] ?? false) {
        $admin_menus['admin_menu'] = $menus['admin_menu'];
        $admin_menus['admin_footer_1'] = $menus['admin_footer_1'];
        $admin_menus['admin_footer_2'] = $menus['admin_footer_2'];
        $admin_menus['admin_menu']['logo']['name'] = '<span class="fas fa-angle-double-left"></span>&nbsp;' . $menus['admin_menu']['logo']['name'];
    } else {
        $admin_menus = json_decode(file_get_contents(__DIR__.'/config/admin_menus.json'), true);

        if ($menus['main']['logo']['name']) {
            $admin_menus['admin_menu']['logo']['name'] = '<span class="fas fa-angle-double-left"></span>&nbsp;' . $menus['main']['logo']['name'];
        }
    }

    $css_classes = [
        'navbar' => 'navbar-expand-md navbar-light bg-primary mb-4 pt-1 pb-0',
        'ul' => 'navbar-nav ml-auto mr-0',
        'li' => 'nav-item',
        'a' => 'nav-link text-white',
        'toggler' => 'navbar-toggler text-white',
    ];
    $hamburger_bars = '<span class="fas fa-bars"></span>';
@endphp

<!doctype html>
<html lang="{{ $lang }}">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta
		name="description"
		content="Content management dashboard interface {{ $app_title }}"
	>
	<title>Wildfire Dashboard {{ $app_title }}</title>
	<link rel="stylesheet" href="https://use.typekit.net/xkh7dxd.css">
	<link rel="stylesheet" href="{{ADMIN_URL}}/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{ADMIN_URL}}/css/wildfire.css">
	<link rel="stylesheet" href="{{ADMIN_URL}}/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="{{ADMIN_URL}}/plugins/datatables/datatables.min.css">
	<link rel="stylesheet" href="{{ADMIN_URL}}/css/custom.css">

    @yield('meta')
</head>

<body>
    <hr class="hr fixed-top" style="margin:0 !important;">

    {!! $theme->get_navbar_menu($admin_menus['admin_menu'], $css_classes, $hamburger_bars) !!}

    <div class="p-3 container">
        @yield('body')
    </div>

    {{-- footer menus --}}
    @include('partials.footer', ['admin_menus' => $admin_menus])

    <script src="{{ ADMIN_URL }}/plugins/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/popper/popper.min.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/moment.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/typeout/typeout.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/datatables/datatables.min.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/clipboard.min.js"></script>
    <script src="{{ ADMIN_URL }}/plugins/keymaster.js"></script>
    <script src="{{ ADMIN_URL }}/js/custom.js?v={{time()}}"></script>
    <script src="https://unpkg.com/draggabilly@2/dist/draggabilly.pkgd.min.js"></script>
    <script src="https://unpkg.com/packery@2/dist/packery.pkgd.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>

    <script src="https://blueimp.github.io/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
    <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
    <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload.js"></script>

    @if (isset($types['webapp']['admin_confetti']))
        <script src="https://cdn.jsdelivr.net/gh/mathusummut/confetti.js/confetti.min.js"></script>
        <script>
            $(document).on('click', '.save_btn', function(e) {
                confetti.start(1000);
            });
        </script>
    @endif

    @if ($type == 'admin' && $slug == 'index')
        <script src="{{ ADMIN_URL }}/js/admin-index.js"></script>
    @endif

    <script src="{{ ADMIN_URL }}/js/list.js"></script>
    <script src="{{ ADMIN_URL }}/js/edit.js"></script>
</body>
</html>
