@if($firstCategories->isNotEmpty() || $secondCategories->isNotEmpty())
    <div class="section-1">
        <div class="container py-4">
            <div class="row">
                <!-- Khối bên trái -->
                <div class="col-md-6  mb-3 mb-md-0">
                    <div class="bg-white rounded-20 p-3 shadow-sm">
                        <div class="row">
                            <!-- 8 phần tử -->
                            @foreach($firstCategories as $category)
                                <div class="col-3 text-center">
                                    <a href="{{ $category->url }}" class="text-dark text-decoration-none ">
                                        <div class="zoom-hover">

                                            <img
                                                src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
                                                class="img-fluid rounded" width="100" height="100"
                                                alt="{{ $category->name }}">
                                            <p class="mt-2 fw-600 fs-14">{{ $category->name }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Khối bên phải -->
                <div class="col-md-6  ">
                    <div class="bg-white rounded-20 p-3 shadow-sm ">
                        <!-- 8 phần tử -->
                        <div class="row">
                            @foreach($secondCategories as $category)

                                <div class="col-3 text-center">
                                    <a href="{{ $category->name=="Review"?route("public.reviews"):$category->url }}"
                                       class="text-dark text-decoration-none ">
                                        <div class="zoom-hover">
                                            <img
                                                src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
                                                class="img-fluid rounded" width="100" height="100"
                                                alt="{{ $category->name }}">
                                            <p class="mt-2 fw-600 fs-14">{{ $category->name }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
