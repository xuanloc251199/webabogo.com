{!! Theme::partial('header') !!}

@if (Theme::get('hasBreadcrumb', true))
    {!! Theme::partial('breadcrumb') !!}
@endif

<main>
    {!! Theme::content() !!}
</main>

{!! Theme::partial('footer') !!}
