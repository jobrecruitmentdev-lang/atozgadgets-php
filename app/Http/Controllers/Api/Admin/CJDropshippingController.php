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
            $order = \App\Services\Cj\CjOrderService::placeOrder($orderId);
            return response()->json([
                'success' => true,
                'message' => 'Order placed with CJ Dropshipping successfully.'
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
            \App\Services\Cj\CjOrderService::cancelOrder($cjOrderId);
            return response()->json([
                'success' => true,
                'message' => "CJ Order {$cjOrderId} cancelled successfully."
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
     * CJ Dropshipping Webhook Receiver (No Auth required)
     */
    public function webhook(Request $request)
    {
        try {
            \App\Services\Cj\CjShipmentService::handleWebhook($request->all());
            return response()->json(['success' => true, 'event_received' => 'order.status_update']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CJ Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}
