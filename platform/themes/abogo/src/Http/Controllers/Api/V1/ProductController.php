<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);

        if ($perPage <= 0) {
            $perPage = 10;
        }

        if ($perPage > 50) {
            $perPage = 50;
        }

        $products = DB::table('ec_products as p')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'p.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\Product');
            })
            ->where('p.is_variation', 0)
            ->where('p.status', 'published')
            ->select([
                'p.id',
                'p.name',
                'p.sku',
                'p.price',
                'p.sale_price',
                'p.image',
                'p.created_at',
                's.key as slug',
            ])
            ->orderByDesc('p.id')
            ->paginate($perPage);

        $items = collect($products->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'sku' => $item->sku,
                'price' => (float) ($item->price ?? 0),
                'sale_price' => (float) ($item->sale_price ?? 0),
                'image' => $this->formatImageUrl($item->image),
                'product_type' => $this->guessProductType($item->name, $item->slug),
                'created_at' => $item->created_at,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully.',
            'data' => $items,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
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
            str_contains($text, 'khách sạn')
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