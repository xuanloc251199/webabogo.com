<div class="faq-section container">
    @foreach($categories as $category)
        <h2 class="faq-category-title">{{ $category->name }}</h2>
        <div class="accordion" id="faq-{{ $category->id }}">
            <!-- Câu hỏi 1 -->
            @foreach($category->faqs as $item)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#item-{{ $item->id }}">
                            {!! BaseHelper::clean($item->question) !!}
                        </button>
                    </h2>
                    <div id="item-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#faq-{{ $category->id }}">
                        <div class="accordion-body">
                            {!! BaseHelper::clean($item->answer) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
