<?php

namespace Botble\Ecommerce\Services\Products;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductAvailabilityService
{
    /**
     * Filter hotels/villas by room availability and location
     * Optimized for large databases
     */
    public function filterHotelsByAvailability(
        ?string $startDate = null,
        ?string $endDate = null,
        array   $places = [],
        ?string $keyword = null,
        array   $categoryIds = []
    ): Builder
    {
        // Step 1: Get available room IDs (variations) using optimized subquery
        $availableRoomIds = $this->getAvailableRoomIds($startDate, $endDate);

        // Step 2: Get hotel IDs from available rooms using index
        $hotelIds = $this->getHotelIdsFromRooms($availableRoomIds);

        // Step 3: Build optimized hotel query
        return $this->buildHotelQuery($hotelIds, $places, $keyword, $categoryIds);
    }


    /**
     * Get available room IDs (product variations) for date range
     * Uses index on configurable_product_id and product quantity
     */
    private function getAvailableRoomIds(?string $startDate, ?string $endDate): array
    {

        if (!$startDate || !$endDate) {
            // If no dates provided, get all rooms with quantity > 0
            return ProductVariation::query()
                ->select('ec_product_variations.id')
                ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
                ->where('ec_products.quantity', '>', 0)
                ->where('ec_products.is_variation', 1)
                ->where('ec_products.status', 'published')
                ->pluck('ec_product_variations.id')
                ->toArray();
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        // Optimized query using joins and indexes
        return ProductVariation::query()
            ->select('ec_product_variations.id')
            ->join('ec_products', 'ec_products.id', '=', 'ec_product_variations.product_id')
            ->where('ec_products.quantity', '>', 0)
            ->where('ec_products.is_variation', 1)
            ->where('ec_products.status', 'published')
            ->where(function (Builder $query) use ($start, $end) {
                // Room is available if:
                // 1. No date restrictions (start_date and end_date are null)
                // 2. Or date range doesn't conflict with booking period
                $query->where(function (Builder $q) {
                    $q->whereNull('ec_products.start_date')
                        ->whereNull('ec_products.end_date');
                })
                    ->orWhere(function (Builder $q) use ($start, $end) {
                        // Check if room is available during requested period
                        $q->where(function (Builder $dateQ) use ($start, $end) {
                            $dateQ->where('ec_products.start_date', '<=', $start)
                                ->where('ec_products.end_date', '>=', $end);
                        })
                            ->orWhere(function (Builder $dateQ) use ($start, $end) {
                                // Or no conflicts with existing bookings
                                $dateQ->where('ec_products.end_date', '<', $start)
                                    ->orWhere('ec_products.start_date', '>', $end);
                            });
                    });
            })
            ->pluck('ec_product_variations.id')
            ->toArray();
    }

    /**
     * Get hotel IDs from available room IDs
     * Uses index on configurable_product_id
     */
    private function getHotelIdsFromRooms(array $roomIds): array
    {
        if (empty($roomIds)) {
            return [];
        }

        // Use single query with index on configurable_product_id
        return ProductVariation::query()
            ->select('configurable_product_id')
            ->whereIn('id', $roomIds)
            ->whereNotNull('configurable_product_id')
            ->distinct()
            ->pluck('configurable_product_id')
            ->toArray();
    }

    /**
     * Build optimized hotel query with all filters
     */
    private function buildHotelQuery(
        array   $hotelIds,
        array   $places = [],
        ?string $keyword = null,
        array   $categoryIds = []
    ): Builder
    {
        $query = Product::query()
            ->select([
                'ec_products.*',
                // Add calculated fields for frontend
                DB::raw('(SELECT COUNT(*) FROM ec_product_variations WHERE configurable_product_id = ec_products.id AND quantity > 0) as available_rooms_count')
            ])
            ->where('is_variation', 0)
            ->wherePublished();

        // Filter by available hotels only if we have date restrictions
//        if (!empty($hotelIds)) {
//            $query->whereIn('id', $hotelIds);
//        }
        // Filter by places using optimized join
        if (!empty($places)) {
            $query->whereHas('places', function (Builder $q) use ($places) {
                $q->whereIn('places.id', $places);
            });
        }

        // Filter by categories
        if (!empty($categoryIds)) {
            $query->whereHas('categories', function (Builder $q) use ($categoryIds) {
                $q->whereIn('ec_product_category_product.category_id', $categoryIds);
            });
        }

        // Filter by keyword
        if ($keyword) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('sku', 'LIKE', "%{$keyword}%");
            });
        }

        // Eager load relationships to avoid N+1 queries
        $query->with([
            'slugable',
            'categories:id,name',
            'places:id,name',
            'variations' => function ($q) {
                $q->select('id', 'product_id', 'configurable_product_id')
                    ->with(['product:id,name,quantity,price,sale_price,sku']);
            }
        ]);

        return $query;
    }

    /**
     * Get room availability details for a specific hotel
     */
    public function getHotelRoomAvailability(int $hotelId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = ProductVariation::query()
            ->select(['id', 'product_id', 'configurable_product_id'])
            ->where('configurable_product_id', $hotelId)
            ->with(['product:id,name,image,sku,quantity,price,sale_price'])
            ->whereHas('product', function (Builder $q) {
                $q->where('quantity', '>', 0)
                    ->where('is_variation', 1)
                    ->where('status', 'published');
            });

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            $query->whereHas('product', function (Builder $q) use ($start, $end) {
                $q->where(function (Builder $subQ) use ($start, $end) {
                    $subQ->where(function (Builder $dateQ) {
                        $dateQ->whereNull('start_date')->whereNull('end_date');
                    })
                        ->orWhere(function (Builder $dateQ) use ($start, $end) {
                            $dateQ->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                });
            });
        }

        return $query->get()->toArray();
    }

    /**
     * Check if a specific hotel has available rooms for date range
     */
    public function isHotelAvailable(int $hotelId, ?string $startDate = null, ?string $endDate = null): bool
    {
        $availableRooms = $this->getHotelRoomAvailability($hotelId, $startDate, $endDate);
        return count($availableRooms) > 0;
    }

    /**
     * Get total available rooms count for hotels
     */
    public function getAvailableRoomsCount(array $hotelIds, ?string $startDate = null, ?string $endDate = null): array
    {
        $availableRoomIds = $this->getAvailableRoomIds($startDate, $endDate);

        if (empty($availableRoomIds)) {
            return array_fill_keys($hotelIds, 0);
        }

        $counts = ProductVariation::query()
            ->select(['configurable_product_id', DB::raw('COUNT(*) as room_count')])
            ->whereIn('ec_product_variations.id', $availableRoomIds)
            ->whereIn('configurable_product_id', $hotelIds)
            ->groupBy('configurable_product_id')
            ->pluck('room_count', 'configurable_product_id')
            ->toArray();

        // Fill missing hotels with 0 count
        return array_merge(array_fill_keys($hotelIds, 0), $counts);
    }
}
