<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = DB::table('ec_product_categories as c')
            ->leftJoin('slugs as s', function ($join) {
                $join->on('s.reference_id', '=', 'c.id')
                    ->where('s.reference_type', '=', 'Botble\\Ecommerce\\Models\\ProductCategory');
            })
            ->select([
                'c.id',
                'c.name',
                'c.description',
                'c.image',
                'c.icon',
                'c.status',
                'c.created_at',
                's.key as slug',
            ])
            ->orderByDesc('c.id')
            ->get();

        $items = collect($rows)->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'description' => $item->description,
                'image' => $this->formatImageUrl($item->image),
                'icon' => $this->formatImageUrl($item->icon),
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully.',
            'total' => $items->count(),
            'data' => $items,
        ]);
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