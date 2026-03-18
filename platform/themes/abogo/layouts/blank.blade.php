<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! Theme::header() !!}
</head>
<body @if (BaseHelper::isRtlEnabled()) dir="rtl" @endif class="blank-page">
{!! apply_filters(THEME_FRONT_BODY, null) !!}

<main >
    {!! Theme::content() !!}
</main>

{!! Theme::footer() !!}
</body>
</html>