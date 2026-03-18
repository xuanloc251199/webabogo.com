<div class="col-12 col-md-6 mt-4">
    <h2 class="fw-700 fs-24">Tầm nhìn, sứ mệnh</h2>
    @foreach(json_decode(theme_option("contact_mission"),true) as $contact_mission)
        <div class="d-flex align-items-center p-3 mb-3 bg-white shadow-sm rounded-20">
            <img
                src="{{ RvMedia::getImageUrl($contact_mission[0]['value']) }}"
                alt="{{ $contact_mission[1]['value'] }}"
                class="me-5 rounded-10"
                style="width: 86px; height: 86px;">
            <div>
                <h5 class="fw-bold mb-1 text-center">{{ $contact_mission[1]['value'] }}</h5>
            </div>
        </div>

    @endforeach
</div>
