<style>
    .select2-container {
        width: 100% !important;
    }
</style>
<div class="review-section container">
    <h1>Nhận xét</h1>
    <div class="bg-white rounded-20 reviews">
        @foreach($reviews as $review)
            <div class="review-item rounded-10 mb-3">
                <div class="review-name">{{$review->customer?$review->customer->name:$review->customer_name}}</div>
                <div class="rating text-warning my-2">
                    @for($i=1 ; $i<=5; $i++)
                        @if($i<=$review->star)
                            <i class="fa-solid fa-star"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>
                <div class="review-detail">{{$review->comment}}</div>
            </div>
        @endforeach
        <div class="text-primary write-reviews">
            <i class="fa-solid fa-pen-to-square "></i>
            Viết nhận xét
        </div>
        <div class="form-reviews d-none">
            <form action="{{ route('public.reviews.create') }}" method="post" enctype="multipart/form-data"
                  class="p-4 border rounded shadow-sm bg-white">
                @csrf

                <h5 class=" mb-3 fw-bold text-primary
                    ">Đánh giá sản phẩm</h5>

                <select name="product_id" id="product_id" class="form-select w-100 select2-product">
                    @foreach($products as $id=>$product_name)
                        <option value="{{$id}}" {{old("product_id") ==$id?"selected":""}}>{{$product_name}}</option>
                    @endforeach
                </select>
                <!-- Star Rating -->
                <div class="mb-3 text-center">
                    <label class="form-label fw-semibold">Đánh giá:</label>
                    <div class="star-rating d-inline-flex">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" {{ old('star') == "$i" ? 'checked' : '' }} name="star"
                                   id="star{{ $i }}" value="{{ $i }}">
                            <label for="star{{ $i }}"><i class="fa fa-star fa-1x"></i></label>
                        @endfor
                    </div>
                    @error('star')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Chọn ảnh minh họa:</label>
                    <input type="file" name="images[]" class="form-control" multiple>
                    @error('images')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Comment -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nhận xét của bạn:</label>
                    <textarea class="form-control" name="comment" rows="4"
                              placeholder="Nhập nhận xét...">{{ old('comment') }}</textarea>
                    @error('comment')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Gửi đánh giá</button>
            </form>

            <!-- CSS -->
            <style>
                .star-rating {
                    direction: rtl;
                }

                .star-rating input {
                    display: none;
                }

                .star-rating label {
                    font-size: 2rem;
                    color: #ccc;
                    cursor: pointer;
                    transition: color 0.2s;
                    margin-right: 0.25rem;
                }

                .star-rating label:hover,
                .star-rating label:hover ~ label {
                    color: gold;
                }

                .star-rating input:checked ~ label {
                    color: #ccc;
                }

                .star-rating input:checked + label,
                .star-rating input:checked + label ~ label {
                    color: gold;
                }
            </style>

        </div>
        {!! $reviews->links(Theme::getThemeNamespace('partials.custom-pagination')) !!}

    </div>
</div>
