---
title: CJ Order Placement Flow
tags: [cj, order, fulfillment, dropshipping]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Order Placement Flow

**Purpose:** Documents how AtoZGadgets creates and submits orders to CJDropshipping on behalf of customers.

**Summary:** `CjOrderService::placeOrder($orderId)` is the single entry point for CJ order fulfillment. It filters only `fulfillment_type='cj'` items from the order, maps shipping address fields to CJ's format, creates the CJ order, then immediately auto-submits it. A `CjOrder` record links our internal order to CJ's `orderId`.

## Source File

- [`app/Services/Cj/CjOrderService.php`](../../app/Services/Cj/CjOrderService.php) — `origin/latest-sync` (L16–L71)

## Order Placement Flow

```
placeOrder($orderId)
  │
  ├── Order::with(['items.product.cjProduct', 'user', 'address'])->findOrFail($orderId)
  │
  ├── [FILTER] Loop order items:
  │     Only include if:
  │       item->product->fulfillment_type === 'cj'
  │       AND item->product->cjProduct exists
  │     Map to: { vid: cj_vid, quantity: qty }
  │
  ├── No CJ products? → throw Exception('No CJ-fulfillable items in this order.')
  │
  ├── Country code detection:
  │     stripos(address->country, 'india') → 'IN', else 'US'
  │
  ├── Build payload:
  │     orderNumber, shippingCountryCode, shippingAddress (line1),
  │     shippingAddress2 (line2 or ''), shippingZip, shippingPhone,
  │     shippingCustomerName (first + last), shippingCity,
  │     shippingProvince, products[]
  │
  ├── POST /shopping/order/createOrder → responseData
  │
  ├── responseData['code'] !== 200 → throw Exception(json_encode(responseData))
  │
  ├── CjOrder::create([
  │     order_id    → $orderId (our DB ID)
  │     cj_order_id → responseData['data']['orderId']
  │     cj_status   → 'created'
  │   ])
  │
  └── self::submitOrder($cjOrderId, $headers)
        POST /shopping/order/submitOrder { orderId: cjOrderId }
        ← Auto-submit immediately after create
```

## Cancel & Get Detail

```php
// Cancel
CjOrderService::cancelOrder($cjOrderId)
  POST /shopping/order/cancelOrder { orderId }
  returns: $data['code'] === 200 (bool)

// Get detail
CjOrderService::getOrderDetail($cjOrderId)
  GET /shopping/order/getOrderDetail { orderId }
  returns: $data['data'] ?? null
```

## Country Code Logic (⚠️ Debt)

```php
$countryCode = stripos($order->address->country, 'india') !== false ? 'IN' : 'US';
```
This is a **hardcoded 2-country check** — any non-India country defaults to `US`. Needs a proper country code lookup for international expansion.

## CjOrder Model Link

| Column | Purpose |
|---|---|
| `order_id` | FK to our `orders` table |
| `cj_order_id` | CJ's internal order ID |
| `cj_status` | `created`, `shipped`, `delivered`, `cancelled` |

`cj_vid` on `CjProduct` model = CJ's variant ID — required in the order payload.

## API Endpoints Used

| Action | Method | Endpoint |
|---|---|---|
| Create order | POST | `/shopping/order/createOrder` |
| Submit order | POST | `/shopping/order/submitOrder` |
| Cancel order | POST | `/shopping/order/cancelOrder` |
| Get detail | GET | `/shopping/order/getOrderDetail` |

## Related
- [[CJ-Dropshipping-MOC]]
- [[CJ-Auth-Token-Flow]]
- [[CJ-Shipment-Tracking-Webhook]]
- `../../app/Services/Cj/CjOrderService.php`
