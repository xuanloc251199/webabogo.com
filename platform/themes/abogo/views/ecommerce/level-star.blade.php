<div class="rating text-warning my-2">
    @for ($i = 1; $i <= 5; $i++)
        @if($i<=$product->level_star)
            <i class="fa-solid fa-star"></i>
        @else
            <i class="fa-regular fa-star"></i>
        @endif
    @endfor
</div>
