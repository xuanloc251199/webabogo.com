<?php

namespace Theme\Abogo\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\Base\Facades\BaseHelper;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = Customer::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'phone' => BaseHelper::clean($data['phone'] ?? null),
                    'password' => Hash::make('12345678'),
                    // thêm các trường khác nếu cần
                ]
            );

            Auth::guard("customer")->login($user, true);
            return redirect()->route("public.index"); // hoặc route bạn muốn
        } catch (\Exception $e) {
            return redirect()->route("customer.login")->withErrors(['msg' => 'Không thể đăng nhập bằng Google']);
        }
    }
}
