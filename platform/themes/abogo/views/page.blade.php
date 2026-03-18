@php
    $page->loadMissing('metadata');

    Theme::set('page', $page);
@endphp

{!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, Html::tag('div', BaseHelper::clean($page->content), ['class' => 'container ck-content'])->toHtml(), $page) !!}
