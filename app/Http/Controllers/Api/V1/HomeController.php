<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $featuredProducts = DB::table('ec_products as p')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'p.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
            })
            ->where('p.is_variation', 0)
            ->select([
                'p.id',
                'p.name',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.description',
                's.key as slug',
            ])
            ->orderByDesc('p.id')
            ->limit(12)
            ->get();

        $categories = DB::table('ec_product_categories as c')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'c.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\ProductCategory');
            })
            ->select([
                'c.id',
                'c.name',
                'c.image',
                'c.icon',
                's.key as slug',
            ])
            ->orderByDesc('c.id')
            ->limit(10)
            ->get();

        $products = collect($featuredProducts)->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'product_type' => $this->guessProductType($item->name, $item->slug),
                'price' => (float) ($item->price ?? 0),
                'sale_price' => (float) ($item->sale_price ?? 0),
                'image' => $this->formatImageUrl($item->image),
                'description' => $item->description,
            ];
        })->values();

        $grouped = [
            'villas' => $products->where('product_type', 'villa')->values(),
            'hotels' => $products->where('product_type', 'hotel')->values(),
            'tours' => $products->where('product_type', 'tour')->values(),
            'others' => $products->where('product_type', 'product')->values(),
        ];

        $categoryItems = collect($categories)->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'image' => $this->formatImageUrl($item->image),
                'icon' => $this->formatImageUrl($item->icon),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Home data fetched successfully.',
            'data' => [
                'banners' => [],
                'categories' => $categoryItems,
                'featured' => $products,
                'sections' => $grouped,
            ],
        ]);
    }

    private function guessProductType(?string $name, ?string $slug): string
    {
        $text = mb_strtolower(trim(($name ?? '') . ' ' . ($slug ?? '')));

        if (str_contains($text, 'tour')) {
            return 'tour';
        }

        if (str_contains($text, 'villa')) {
            return 'villa';
        }

        if (
            str_contains($text, 'hotel') ||
            str_contains($text, 'khach-san') ||
            str_contains($text, 'khách sạn') ||
            str_contains($text, 'khach san')
        ) {
            return 'hotel';
        }

        return 'product';
    }

    private function formatImageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        return url('storage/' . ltrim($image, '/'));
    }
}