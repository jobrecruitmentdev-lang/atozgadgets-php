<?php

namespace App\Services\Order;

use App\Models\Order;

class CustomerOrderStatusResolver
{
    /**
     * Resolve customer-friendly status presentation from 3 internal dimensions:
     * Payment, Fulfillment, and Shipment.
     */
    public static function resolve(Order $order): array
    {
        $paymentStatus = strtoupper($order->payment_status ?? 'PENDING');
        $fulfillments = $order->fulfillments;

        if (in_array($paymentStatus, ['FAILED', 'ERROR', 'CANCELLED'])) {
            return [
                'status' => 'Payment Failed',
                'badge' => 'badge-danger',
                'badge_class' => 'status-danger',
                'description' => 'Payment could not be processed. Please retry with another payment method.',
                'progress_step' => 1,
            ];
        }

        if (in_array($paymentStatus, ['REFUNDED', 'PARTIALLY_REFUNDED'])) {
            return [
                'status' => 'Refunded',
                'badge' => 'badge-secondary',
                'badge_class' => 'status-secondary',
                'description' => 'This order has been refunded.',
                'progress_step' => 1,
            ];
        }

        if (!in_array($paymentStatus, ['PAID', 'COMPLETED', 'SUCCESS'])) {
            return [
                'status' => 'Payment Pending',
                'badge' => 'badge-warning',
                'badge_class' => 'status-pending',
                'description' => 'We are waiting for payment confirmation.',
                'progress_step' => 1,
            ];
        }

        // Check shipments
        $shipmentStatuses = [];
        if ($fulfillments && $fulfillments->isNotEmpty()) {
            foreach ($fulfillments as $f) {
                foreach ($f->shipments as $s) {
                    $st = $s->shipment_status ?? ($s->status ?? '');
                    if (!empty($st)) {
                        $shipmentStatuses[] = strtoupper($st);
                    }
                }
            }
        }
        if ($order->shipment) {
            $st = $order->shipment->shipment_status ?? ($order->shipment->status ?? '');
            if (!empty($st)) {
                $shipmentStatuses[] = strtoupper($st);
            }
        }

        if (!empty($shipmentStatuses)) {
            if (collect($shipmentStatuses)->every(fn($st) => in_array($st, ['DELIVERED', 'COMPLETED']))) {
                return [
                    'status' => 'Delivered',
                    'badge' => 'badge-success',
                    'badge_class' => 'status-paid',
                    'description' => 'Your order has been successfully delivered.',
                    'progress_step' => 4,
                ];
            }

            if (collect($shipmentStatuses)->contains(fn($st) => in_array($st, ['OUT_FOR_DELIVERY', 'IN_TRANSIT']))) {
                return [
                    'status' => 'In Transit',
                    'badge' => 'badge-primary',
                    'badge_class' => 'status-processing',
                    'description' => 'Your package is on its way to your destination.',
                    'progress_step' => 3,
                ];
            }

            if (collect($shipmentStatuses)->contains(fn($st) => in_array($st, ['SHIPPED', 'LABEL_CREATED', 'DISPATCHED']))) {
                return [
                    'status' => 'Shipped',
                    'badge' => 'badge-primary',
                    'badge_class' => 'status-processing',
                    'description' => 'Your package has been dispatched from the fulfillment center.',
                    'progress_step' => 3,
                ];
            }
        }

        // Check fulfillments
        $fulfillmentStatuses = $fulfillments ? $fulfillments->pluck('fulfillment_status')->map(fn($s) => strtoupper($s))->toArray() : [];
        if (in_array('SUBMITTED', $fulfillmentStatuses) || in_array('PROCESSING', $fulfillmentStatuses) || strtolower($order->status ?? '') === 'processing') {
            return [
                'status' => 'Preparing Order',
                'badge' => 'badge-info',
                'badge_class' => 'status-processing',
                'description' => 'Your order is confirmed and currently being prepared for dispatch.',
                'progress_step' => 2,
            ];
        }

        return [
            'status' => 'Order Confirmed',
            'badge' => 'badge-success',
            'badge_class' => 'status-paid',
            'description' => 'We have received your order and payment.',
            'progress_step' => 2,
        ];
    }
}
