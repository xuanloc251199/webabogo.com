<style>
    li.page-item {
        margin-right: 5px;
    }
    .page-link-new {
        text-decoration: none;
        color: #000;
        width: 30px;
        display: block;
        text-align: center;
        height: 30px;
        line-height: 30px;
    }

    li.page-item.active .page-link-new {
        background-color: #398CE5;
        border-radius: 50%;
        color: #fff;
    }

    li.page-item .page-link-new:hover {
        background-color: #398CE5;
        border-radius: 50%;
        color: #fff;
    }

    li.page-item .prev, li.page-item .next {
        background-color: #fff;
        border-radius: 50%;
    }
</style>
<div class="pagination justify-content-center mt-3">
    @if ($paginator->hasPages())
        <div class="pagination-area mt-15 mb-md-5 mb-lg-0 pagination-page">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    @if (!$paginator->onFirstPage())
                        <li class="page-item">
                            <a class="prev page-link-new" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i
                                    class="fa fa-angle-left"></i></a>
                        </li>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <li class="page-item">
                                <span class="page-link-new">{{ $element }}</span>
                            </li>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active"><span class="page-link-new">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link-new" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <li class="page-item"><a class="next page-link-new" href="{{ $paginator->nextPageUrl() }}"
                                                 rel="next"><i class="fa fa-angle-right"></i></a></li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>
