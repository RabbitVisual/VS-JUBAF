<?php

namespace Modules\Marketplace\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Modules\Marketplace\Models\MarketplaceCustomer;
use Modules\Marketplace\Models\Order;

class MarketplaceCustomerAuthController extends Controller
{
    public function showRegister()
    {
        return view('marketplace::customer.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:marketplace_customers,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
        ]);

        $customer = MarketplaceCustomer::create($data);

        Auth::guard('marketplace')->login($customer);

        return redirect()->route('marketplace.customer.dashboard')
            ->with('success', __('marketplace::messages.customer_registered'));
    }

    public function showLogin()
    {
        return view('marketplace::customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard('marketplace')->attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => __('auth.failed')])
                ->withInput($request->only('email', 'remember'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('marketplace.customer.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('marketplace')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('marketplace.storefront.index')
            ->with('info', __('marketplace::messages.customer_logged_out'));
    }

    public function dashboard()
    {
        /** @var \Modules\Marketplace\Models\MarketplaceCustomer $customer */
        $customer = Auth::guard('marketplace')->user();

        $orders = Order::where('marketplace_customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view('marketplace::customer.dashboard', compact('customer', 'orders'));
    }

    public function profile()
    {
        /** @var \Modules\Marketplace\Models\MarketplaceCustomer $customer */
        $customer = Auth::guard('marketplace')->user();

        return view('marketplace::customer.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \Modules\Marketplace\Models\MarketplaceCustomer $customer */
        $customer = Auth::guard('marketplace')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
            'address_default' => ['nullable', 'array'],
            'address_default.cep' => ['nullable', 'string', 'max:20'],
            'address_default.street' => ['nullable', 'string', 'max:255'],
            'address_default.number' => ['nullable', 'string', 'max:50'],
            'address_default.complement' => ['nullable', 'string', 'max:255'],
            'address_default.district' => ['nullable', 'string', 'max:255'],
            'address_default.city' => ['nullable', 'string', 'max:255'],
            'address_default.state' => ['nullable', 'string', 'max:2'],
        ]);

        $customer->update($data);

        return redirect()->route('marketplace.customer.profile')
            ->with('success', __('marketplace::messages.profile_updated'));
    }
}

