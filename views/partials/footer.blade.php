@php
    $theme = new Wildfire\Core\Theme;
@endphp

<footer class="pt-4 pt-md-5 bg-white">
    <hr class="bg-primary" style="background-image: none;">

    <div class="container my-5">
        <div class="row">
            @if (!$types['webapp']['hide_wildfire_logo'])
                <div class="col-md">
                    <a href="https://wildfire.world">
                        <img class="w-40" src="{{ ADMIN_URL }}/img/logo.png">
                    </a>

                    <p class="text-muted small mb-3 mt-4 pr-5">
                        Made with <span class="fas fa-heart"></span>
                        @if ($app_title)
                            <br><em>for {{$app_title}}</em>
                        @endif
                    </p>

                    <p class="text-muted small my-3 pr-5">
                        Wildfire is a technology consultancy based in New Delhi, India
                    </p>

                    <p class="text-muted small my-3 pr-5">
                        {{$year = date('Y')}} &copy; {{$year == '2020' ? $year : "2020 - $year"}}
                    </p>
                </div>
            @endif

            <div class="col-md">
                {!!
                    $theme->get_menu(
                        $admin_menus['admin_footer_1'],
                        [
                            'ul' => 'list-unstyled mt-5 pt-2 pl-md-5',
                            'li' => '',
                            'a' => 'small'
                        ]
                    )
                !!}
            </div>

            <div class="col-md">
                {!!
                    $theme->get_menu(
                        $admin_menus['admin_footer_2'],
                        [
                            'ul' => 'list-unstyled mt-5 pt-2 pl-md-5',
                            'li' => '',
                            'a' => 'small'
                        ]
                    );
                !!}
            </div>
        </div>
    </div>
</footer>
