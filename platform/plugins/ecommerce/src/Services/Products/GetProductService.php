<?php

namespace Botble\Ecommerce\Services\Products;

use Botble\Base\Facades\BaseHelper;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Services\Products\ProductAvailabilityService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class GetProductService
{
    public function __construct(
        protected ProductInterface           $productRepository,
        protected ProductAvailabilityService $availabilityService
    )
    {
    }

    public function getProduct(
        Request $request,
                $category = null,
                $brand = null,
        array   $with = [],
        array   $withCount = [],
        array   $conditions = []
    ): Collection|LengthAwarePaginator
    {
        $num = $request->integer('num') ?: $request->integer('per-page');
        $shows = EcommerceHelper::getShowParams();
        if (!array_key_exists($num, $shows)) {
            $num = (int)theme_option('number_of_products_per_page', 8);
        }

        $queryVar = [
            'keyword' => BaseHelper::stringify($request->input('q')),
            'brands' => (array)$request->input('brands', []),
            'categories' => (array)$request->input('categories', []),
            'places' => (array)$request->input('places', []),
            'tags' => (array)$request->input('tags', []),
            'collections' => (array)$request->input('collections', []),
            'attributes' => (array)$request->input('attributes', []),
            'max_price' => $request->input('max_price'),
            'min_price' => $request->input('min_price'),
            'level_star' => $request->input('level_star'),
            'price_ranges' => (array)$request->input('price_ranges', []),
            'sort_by' => $request->input('sort_by'),
            'room_amenities' => $request->input('room_amenities') ? explode(',', $request->input('room_amenities')) : [],//Tện ích phòng
            'num' => $num,
            // Date filtering for hotel/villa availability
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'filter_by_availability' => $request->boolean('filter_by_availability', false),
        ];
        if ($category) {
            $queryVar['categories'] = array_merge($queryVar['categories'], [$category]);
        }
        if ($brand) {
            $queryVar['brands'] = array_merge(($queryVar['brands']), [$brand]);
        }

        if ($request->has('places')) {
            $queryVar['places'] = $request->input('places', []);
        }

        $orderBy = [
            'ec_products.order' => 'ASC',
            'ec_products.created_at' => 'DESC',
        ];


        if (!EcommerceHelper::isReviewEnabled() && in_array($queryVar['sort_by'], ['rating_asc', 'rating_desc'])) {
            $queryVar['sort_by'] = 'date_desc';
        }

        $params = array_merge([
            'paginate' => [
                'per_page' => $queryVar['num'],
                'current_paged' => $request->integer('page', 1) ?: 1,
            ],
            'with' => array_merge(EcommerceHelper::withProductEagerLoadingRelations(), $with),
            'withCount' => $withCount,
        ], EcommerceHelper::withReviewsParams());

        if (!empty($conditions)) {
            $params['condition'] = $conditions;
        }
        // Check if we need to filter by room availability (for hotels/villas)
        if ($queryVar['filter_by_availability'] && ($queryVar['start_date'] || $queryVar['end_date'])) {

            $products = $this->getProductsWithAvailabilityFilter($queryVar, $params, $orderBy);
        } else {

            switch ($queryVar['sort_by']) {
                case 'level_star_asc':
                    $orderBy = [
                        'ec_products.level_star' => 'ASC',
                    ];

                    break;
                case 'level_star_desc':
                    $orderBy = [
                        'ec_products.level_star' => 'DESC',
                    ];

                    break;
                case 'date_asc':
                    $orderBy = [
                        'ec_products.created_at' => 'ASC',
                    ];

                    break;
                case 'date_desc':
                    $orderBy = [
                        'ec_products.created_at' => 'DESC',
                    ];

                    break;
                case 'price_asc':
                    $orderBy = [
                        'products_with_final_price.price' => 'ASC',
                    ];

                    break;
                case 'price_desc':
                    $orderBy = [
                        'products_with_final_price.price' => 'DESC',
                    ];

                    break;
                case 'name_asc':
                    $orderBy = [
                        'ec_products.name' => 'ASC',
                    ];

                    break;
                case 'name_desc':
                    $orderBy = [
                        'ec_products.name' => 'DESC',
                    ];

                    break;
                case 'rating_asc':
                    if (EcommerceHelper::isReviewEnabled()) {
                        $orderBy = [
                            'reviews_avg' => 'ASC',
                        ];
                    }

                    break;
                case 'rating_desc':
                    if (EcommerceHelper::isReviewEnabled()) {
                        $orderBy = [
                            'reviews_avg' => 'DESC',
                        ];
                    }

                    break;
            }
            $products = $this->productRepository->filterProducts([
                'keyword' => $queryVar['keyword'],
                'min_price' => $queryVar['min_price'],
                'max_price' => $queryVar['max_price'],
                'price_ranges' => array_values($queryVar['price_ranges']),
                'categories' => $queryVar['categories'],
                'places' => $queryVar['places'],
                'tags' => $queryVar['tags'],
                'collections' => $queryVar['collections'],
                'brands' => $queryVar['brands'],
                'attributes' => $queryVar['attributes'],
                'order_by' => $orderBy,
                'is_variation' => 0,
                'room_amenities' => $queryVar["room_amenities"],
                'level_star' => $queryVar["level_star"],
            ], $params);
        }

        if ($keyword = $queryVar['keyword']) {
            $products->setCollection(BaseHelper::sortSearchResults($products->getCollection(), $keyword, 'name'));
        }

        return $products;
    }

    public function suggestSearch($keyword, $with = [])
    {
        return $this->productRepository->filterProducts(['keyword' => $keyword], ["take" => 10]);
    }

    /**
     * Get products with availability filtering for hotels/villas
     * Optimized for large databases
     */
    protected function getProductsWithAvailabilityFilter(array $queryVar, array $params, array $orderBy)
    {
        switch ($queryVar['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    'ec_products.id' => 'ASC',
                ];

                break;
            case 'date_desc':
                $orderBy = [
                    'ec_products.id' => 'DESC',
                ];

                break;
            case 'price_asc':
                $orderBy = [
                    'ec_products.price' => 'ASC',
                ];

                break;
            case 'price_desc':
                $orderBy = [
                    'ec_products.price' => 'DESC',
                ];
                break;
            case 'name_asc':
                $orderBy = [
                    'ec_products.name' => 'ASC',
                ];

                break;
            case 'name_desc':
                $orderBy = [
                    'ec_products.name' => 'DESC',
                ];

                break;
            case 'level_star_asc':
                $orderBy = [
                    'ec_products.level_star' => 'ASC',
                ];

                break;
            case 'level_star_desc':
                $orderBy = [
                    'ec_products.level_star' => 'DESC',
                ];

                break;
            case 'rating_asc':
                if (EcommerceHelper::isReviewEnabled()) {
                    $orderBy = [
                        'reviews_avg' => 'ASC',
                    ];
                }

                break;
            case 'rating_desc':
                if (EcommerceHelper::isReviewEnabled()) {
                    $orderBy = [
                        'reviews_avg' => 'DESC',
                    ];
                }

                break;
        }
        // Use availability service to get filtered hotel query
        $hotelQuery = $this->availabilityService->filterHotelsByAvailability(
            $queryVar['start_date'],
            $queryVar['end_date'],
            $queryVar['places'],
            $queryVar['keyword'],
            $queryVar['categories']
        );
        // Apply additional filters
        if ($queryVar['brands']) {
            $hotelQuery->whereIn('brand_id', $queryVar['brands']);
        }

        if ($queryVar['tags']) {
            $hotelQuery->whereHas('tags', function ($q) use ($queryVar) {
                $q->whereIn('ec_product_tag_product.tag_id', $queryVar['tags']);
            });
        }
        if (!empty($queryVar['room_amenities'])) {

            $hotelQuery->whereHas("extensions", function ($q) use ($queryVar) {
                $q->whereIn('extensions.id', $queryVar['room_amenities']);
            });
        }

        if ($queryVar['collections']) {
            $hotelQuery->whereHas('productCollections', function ($q) use ($queryVar) {
                $q->whereIn('ec_product_collection_products.product_collection_id', $queryVar['collections']);
            });
        }

        if ($queryVar['level_star']) {
            $hotelQuery->where("level_star", ">=", $queryVar['level_star']);
        }
        // Apply price filters
        if ($queryVar['min_price'] || $queryVar['max_price']) {

            $hotelQuery->where(function ($q) use ($queryVar) {
                if ($queryVar['min_price']) {
                    $q->where('price', '>=', $queryVar['min_price']);
                }
                if ($queryVar['max_price']) {
                    $q->where('price', '<=', $queryVar['max_price']);
                }
            });
        }

        // Apply sorting
        foreach ($orderBy as $column => $direction) {
            if ($column == "reviews_avg") {
                $hotelQuery->withAvg("reviews", "star")->orderBy("reviews_avg_star", $direction);
            } else {
                $hotelQuery->orderBy($column, $direction);
            }

        }

        // Apply pagination
        $perPage = $params['paginate']['per_page'] ?? 8;
        $currentPage = $params['paginate']['current_paged'] ?? 1;

        return $hotelQuery->where("is_variation",0)->paginate($perPage, ['*'], 'page', $currentPage);
    }
}
