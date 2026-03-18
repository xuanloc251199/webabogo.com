<div class="search-item p-2 cursor-pointer">

    @if(isset($products) && $products->count()>0)
        @foreach($products as $product)
            <div class="fw-500 mb-3 hover-bg-light">
                <a class="text-dark d-flex align-items-center text-decoration-none" href="{{$product->url}}">
                    <img
                        src="{{ RvMedia::getImageUrl($product->image,null,false, RvMedia::getDefaultImage()) }}"
                        class="rounded-10"
                        style="width: 50px"
                        alt="{{ $product->name }}">
                    <span>{{$product->name}}</span>
                </a>
            </div>
        @endforeach
    @else
        <div class="fw-500 mb-3 hover-bg-light">Không tìm thấy kết quả nào!</div>
    @endif
</div>
