<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function show(Request $request): JsonResponse
    {
        $customer = $request->user();

        $stats = $this->getOrderStats($customer->id);
        $recentOrders = $this->getOrdersData($customer->id, 5);

        return $this->successResponse([
            'customer' => new CustomerResource($customer),
            'social_links' => [
                'facebook_url' => $customer->facebook_url,
                'instagram_url' => $customer->instagram_url,
                'youtube_url' => $customer->youtube_url,
                'tiktok_url' => $customer->tiktok_url,
                'twitter_url' => $customer->twitter_url,
                'linkedin_url' => $customer->linkedin_url,
                'wechat_url' => $customer->wechat_url,
            ],
            'stats' => $stats,
            'recent_orders' => $recentOrders,
        ], 'Lấy dữ liệu profile thành công.');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $customer = $request->user();

        $customer->fill($request->validated());
        $customer->save();

        return $this->successResponse([
            'customer' => new CustomerResource($customer->fresh()),
            'social_links' => [
                'facebook_url' => $customer->facebook_url,
                'instagram_url' => $customer->instagram_url,
                'youtube_url' => $customer->youtube_url,
                'tiktok_url' => $customer->tiktok_url,
                'twitter_url' => $customer->twitter_url,
                'linkedin_url' => $customer->linkedin_url,
                'wechat_url' => $customer->wechat_url,
            ],
        ], 'Cập nhật profile thành công.');
    }

    public function orders(Request $request): JsonResponse
    {
        $customer = $request->user();

        return $this->successResponse(
            $this->getOrdersData($customer->id, 20),
            'Lấy danh sách đơn hàng thành công.'
        );
    }

    private function getOrderStats(int $customerId): array
    {
        $base = DB::table('ec_orders')->where('user_id', $customerId);

        return [
            'total_orders' => (clone $base)->count(),
            'pending_orders' => (clone $base)->where('status', 'pending')->count(),
            'processing_orders' => (clone $base)->where('status', 'processing')->count(),
            'completed_orders' => (clone $base)
                ->where(function ($query) {
                    $query->where('status', 'completed')
                        ->orWhereNotNull('completed_at')
                        ->orWhere('is_finished', 1);
                })
                ->count(),
        ];
    }

    private function getOrdersData(int $customerId, int $limit = 20): array
    {
        $orders = DB::table('ec_orders as o')
            ->leftJoin('ec_order_addresses as oa', 'oa.order_id', '=', 'o.id')
            ->where('o.user_id', $customerId)
            ->select(
                'o.id',
                'o.code',
                'o.status',
                'o.amount',
                'o.sub_total',
                'o.shipping_amount',
                'o.discount_amount',
                'o.created_at',
                'o.updated_at',
                'o.is_confirmed',
                'o.is_finished',
                'o.completed_at',
                'oa.name as contact_name',
                'oa.phone as contact_phone',
                'oa.email as contact_email'
            )
            ->orderByDesc('o.id')
            ->limit($limit)
            ->get();

        return $orders->map(function ($order) {
            $items = DB::table('ec_order_product')
                ->where('order_id', $order->id)
                ->select(
                    'id',
                    'product_id',
                    'product_name',
                    'product_image',
                    'qty',
                    'price',
                    'options'
                )
                ->orderBy('id')
                ->get()
                ->map(function ($item) {
                    $options = $this->decodeJson($item->options);

                    return [
                        'id' => (int) $item->id,
                        'product_id' => $item->product_id ? (int) $item->product_id : null,
                        'product_name' => $item->product_name,
                        'product_image' => $this->normalizeImageUrl($item->product_image),
                        'qty' => (int) $item->qty,
                        'price' => (float) $item->price,
                        'booking_dates' => array_values($options['days'] ?? []),
                        'adults_number' => (int) ($options['adults_number'] ?? 0),
                        'children_number' => (int) ($options['children_number'] ?? 0),
                    ];
                })
                ->values()
                ->all();

            return [
                'id' => (int) $order->id,
                'code' => $order->code,
                'status' => $order->status,
                'status_label' => $this->mapStatusLabel(
                    (string) $order->status,
                    (bool) $order->is_finished,
                    $order->completed_at
                ),
                'amount' => (float) $order->amount,
                'sub_total' => (float) $order->sub_total,
                'shipping_amount' => (float) ($order->shipping_amount ?? 0),
                'discount_amount' => (float) ($order->discount_amount ?? 0),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'contact_name' => $order->contact_name,
                'contact_phone' => $order->contact_phone,
                'contact_email' => $order->contact_email,
                'items_count' => count($items),
                'items' => $items,
            ];
        })->values()->all();
    }

    private function decodeJson(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    private function mapStatusLabel(string $status, bool $isFinished = false, ?string $completedAt = null): string
    {
        if ($status === 'completed' || $isFinished || ! empty($completedAt)) {
            return 'Hoàn tất';
        }

        return match ($status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'cancelled' => 'Đã hủy',
            default => ucfirst($status),
        };
    }
}