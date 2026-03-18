@php
    $rating = $rating ?? ($product->reviews_avg ?? 0);
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
@endphp

<div class="rating text-warning my-2">
    @for ($i = 0; $i < $fullStars; $i++)
        <i class="fa-solid fa-star"></i>
    @endfor

    @if ($halfStar)
        <i class="fa-solid fa-star-half-stroke"></i>
    @endif

    @for ($i = 0; $i < $emptyStars; $i++)
        <i class="fa-regular fa-star"></i>
    @endfor
</div>
