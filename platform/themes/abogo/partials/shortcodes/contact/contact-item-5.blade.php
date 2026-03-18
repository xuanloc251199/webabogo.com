<div class="col-12 mt-4">
    <h2 class="fw-700 fs-24">Đối tác</h2>
    <div class="bg-white rounded-20 px-md-5 px-3 py-3 partners d-flex flex-wrap gap-3">
        @if (($partners = theme_option('to_partners')) && $partners = json_decode($partners, true))
            @foreach($partners as $partner)
                <div class="partner-item">
                    <img src="{{ RvMedia::getImageUrl($partner[0]['value']) }}" alt="{{ $partner[1]['value'] }}">
                </div>
            @endforeach
        @endif
    </div>
</div>
