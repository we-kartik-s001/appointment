@extends("vaahcms::backend.vaahone.layouts.backend")

@section('vaahcms_extend_backend_css')

@endsection


@section('vaahcms_extend_backend_js')

    @if(env('THEME_AUTHENTICATION_ENV') == 'develop')
        <script src="http://localhost:8710/authentication/assets/build/app.js" defer></script>
    @else
        <script src="{{vh_theme_assets_url("Authentication", "build/app.js")}}"></script>
    @endif

@endsection

@section('content')

    <div id="appAuthentication">

        <router-view></router-view>

        <vue-progress-bar></vue-progress-bar>

    </div>

@endsection
