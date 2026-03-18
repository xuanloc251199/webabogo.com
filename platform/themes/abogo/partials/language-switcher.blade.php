@php
    $supportedLocales = Language::getSupportedLocales();
    if (empty($options)) {
        $options = [
            'before' => '',
            'lang_flag' => true,
            'lang_name' => true,
            'class' => '',
            'after' => '',
        ];
    }
@endphp

@if ($supportedLocales && count($supportedLocales) > 1)
    @php
        $languageDisplay = setting('language_display', 'all');
    @endphp
    <div class="dropdown me-1">
        <button class="btn dropdown-toggle d-flex align-items-center" type="button"
                data-bs-toggle="dropdown">
            <div class="mobile-hidden">
                @if (Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag'))
                    {!! language_flag(Language::getCurrentLocaleFlag(), Language::getCurrentLocaleName(), 20) !!}
                @endif
                @if (Arr::get($options, 'lang_name', true) && ($languageDisplay == 'all' || $languageDisplay == 'name'))
                    &nbsp;{{ Language::getCurrentLocaleName() }}
                @endif
            </div>
            <div class="desktop-hidden">
                <i class="fa-solid fa-globe fs-22 pt-1"></i>
            </div>
        </button>
        <ul class="dropdown-menu">
            @foreach ($supportedLocales as $localeCode => $properties)
                @if ($localeCode != Language::getCurrentLocale())
                    <li>
                        <a href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}" class="dropdown-item">
                            @if (Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag'))
                                {!! language_flag($properties['lang_flag'], $properties['lang_name'], 20) !!}
                            @endif
                            @if (Arr::get($options, 'lang_name', true) && ($languageDisplay == 'all' || $languageDisplay == 'name'))
                                &nbsp;<span>{{ $properties['lang_name'] }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

@endif
