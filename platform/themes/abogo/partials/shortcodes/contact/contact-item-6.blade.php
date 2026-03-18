<div class="col-12 mt-4">
    <h2 class="fw-700 fs-24">Giải thưởng</h2>
    <div class="awards row">

        @if (($awards = theme_option('to_awards')) && $awards = json_decode($awards, true))
            @foreach($awards as $award)
                <div class="col-2">
                    <div class="award-item">
                        <img src="{{ RvMedia::getImageUrl($award[0]['value']) }}" alt="{{ $award[1]['value'] }}" class="rounded-10">
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
