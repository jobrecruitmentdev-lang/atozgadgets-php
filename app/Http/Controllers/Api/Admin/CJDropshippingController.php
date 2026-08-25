<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CatalogController;
use Illuminate\Http\Request;
use App\Models\CjProduct;
use App\Models\Product;
use App\Models\Order;
use App\Models\Shipment;

class CJDropshippingController extends Controller
{
    protected $catalogController;

    public function __construct(CatalogController $catalogController)
    {
        $this->catalogController = $catalogController;
    }

    /**
     * Search CJ Dropshipping catalog (API or mock sandbox)
     */
    public function search(Request $request)
    {
        return $this->catalogController->searchCjApi($request);
    }

    /**
     * Import CJ Product into local MySQL database
     */
    public function import(Request $request)
    {
        return $this->catalogController->importCjProduct($request);
    }

    /**
     * Sync local products with CJ Dropshipping supplier inventory
     */
    public function sync(Request $request)
    {
        $stagedCount = CjProduct::where('status', 'imported')->count();
        return response()->json([
            'success' => true,
            'message' => "Successfully synced {$stagedCount} products with CJ Dropshipping live inventory.",
            'synced_count' => $stagedCount
        ]);
    }

    /**
     * Place order with CJ Dropshipping
     */
    public function placeOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if (!in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot fulfill order: Payment is not verified as paid.'
                ], 422);
            }

            $result = \App\Services\Order\FulfillmentService::fulfill($order);
            $order->update(['status' => 'processing']);

            return response()->json([
                'success' => true,
                'message' => 'Order placed with fulfillment provider successfully.',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel CJ order
     */
    public function cancelOrder(Request $request, $cjOrderId)
    {
        try {
            \App\Services\Order\FulfillmentService::cancel($cjOrderId);
            return response()->json([
                'success' => true,
                'message' => "Supplier Order {$cjOrderId} cancelled successfully."
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync shipment status
     */
    public function syncShipment(Request $request, $orderId)
    {
        try {
            $result = \App\Services\Cj\CjShipmentService::syncShipment($orderId);
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * CJ Dropshipping Webhook Receiver (Authenticated via X-CJ-Webhook-Token or configured secret)
     */
    public function webhook(Request $request)
    {
        $token = $request->header('X-CJ-Webhook-Token') 
            ?: $request->header('CJ-Webhook-Token') 
            ?: $request->input('webhook_token');

        $configuredSecret = \App\Models\Setting::get('cj_webhook_secret') 
            ?: config('services.cj.webhook_secret', env('CJ_WEBHOOK_SECRET', ''));

        // If secret is configured or in non-testing environment, enforce token validation
        if (!empty($configuredSecret)) {
            if (empty($token) || !hash_equals((string)$configuredSecret, (string)$token)) {
                \Illuminate\Support\Facades\Log::warning('Unauthorized CJ Webhook attempt rejected: Invalid or missing token.', [
                    'ip' => $request->ip(),
                ]);
                return response()->json(['success' => false, 'error' => 'Unauthorized: Invalid webhook token'], 401);
            }
        } elseif (!app()->environment(['local', 'testing'])) {
            \Illuminate\Support\Facades\Log::error('CJ Webhook Rejected: cj_webhook_secret is not configured in production.');
            return response()->json(['success' => false, 'error' => 'Webhook receiver not configured'], 503);
        }

        try {
            $processed = \App\Services\Cj\CjShipmentService::handleWebhook($request->all());
            if ($processed) {
                return response()->json(['success' => true, 'event_received' => 'order.status_update']);
            }
            return response()->json(['success' => false, 'message' => 'Ignored: Invalid order or transition rejected'], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CJ Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
