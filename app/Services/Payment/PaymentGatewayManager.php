<?php

namespace App\Services\Payment;

use App\Models\Setting;

class PaymentGatewayManager
{
    /**
     * Get the dynamically available customer payment methods based on active environment and configuration.
     */
    public static function customerAvailableMethods(): array
    {
        $methods = [];

        // 1. Check PayPal
        $paypalMode = Setting::get('paypal_mode', config('paypal.mode', 'sandbox'));
        $paypalClientId = ($paypalMode === 'live')
            ? Setting::get('paypal_live_client_id', config('paypal.live.client_id', ''))
            : Setting::get('paypal_sandbox_client_id', config('paypal.sandbox.client_id', ''));

        // If client ID is present or in sandbox fallback, enable PayPal
        if (!empty($paypalClientId) || $paypalMode === 'sandbox') {
            $methods[] = [
                'id' => 'paypal',
                'name' => 'PayPal',
                'badge' => $paypalMode === 'sandbox' ? 'PayPal (Sandbox)' : 'PayPal',
                'mode' => $paypalMode,
                'is_live' => ($paypalMode === 'live'),
                'icon' => 'credit-card',
                'description' => 'Pay securely via PayPal, Credit Card or Debit Card.',
            ];
        }

        // 2. Check Stripe (Only enabled if explicit Stripe API keys are configured)
        $stripeKey = Setting::get('stripe_publishable_key', config('services.stripe.key', ''));
        if (!empty($stripeKey) && !str_contains($stripeKey, 'placeholder')) {
            $methods[] = [
                'id' => 'stripe',
                'name' => 'Credit / Debit Card',
                'badge' => 'Stripe Card',
                'mode' => str_starts_with($stripeKey, 'pk_live') ? 'live' : 'sandbox',
                'is_live' => str_starts_with($stripeKey, 'pk_live'),
                'icon' => 'shield-check',
                'description' => 'Direct 256-bit encrypted card checkout.',
            ];
        }

        // Fallback default if nothing configured
        if (empty($methods)) {
            $methods[] = [
                'id' => 'paypal',
                'name' => 'PayPal Secure Checkout',
                'badge' => 'PayPal',
                'mode' => 'sandbox',
                'is_live' => false,
                'icon' => 'credit-card',
                'description' => 'Pay securely with PayPal.',
            ];
        }

        return $methods;
    }

    /**
     * Get customer-safe trust text for product page and checkout.
     */
    public static function getTrustHeadline(): string
    {
        $methods = self::customerAvailableMethods();
        $names = array_column($methods, 'name');

        if (count($names) === 1) {
            return "Secure Checkout via {$names[0]}";
        }

        return "Secure Checkout via " . implode(' & ', $names);
    }
}