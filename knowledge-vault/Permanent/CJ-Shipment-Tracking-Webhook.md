---
title: CJ Shipment Tracking & Webhook
tags: [cj, shipment, tracking, webhook, dropshipping]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Shipment Tracking & Webhook

**Purpose:** Documents both the polling-based shipment sync (`syncShipment`) and the webhook-based push flow (`handleWebhook`) for CJ order tracking.

**Summary:** `CjShipmentService` provides two paths to update shipment status: (1) a manual/cron poll that queries CJ's logistics API, and (2) a webhook handler for CJ push notifications. Both paths write to the `shipments` table. A noted schema decision: `cj_shipments` table was removed — tracking data is stored directly in `shipments`.

## Source File

- [`app/Services/Cj/CjShipmentService.php`](../../app/Services/Cj/CjShipmentService.php) — `origin/latest-sync` (L11–L118)

## Path 1: Polling — syncShipment($orderId)

```
syncShipment($orderId)
  │
  ├── CjOrder::where('internal_order_id', $orderId)->first()
  │     ← Not found → throw Exception
  │
  ├── GET /logistic/order/list { orderNum: cj_order_id }
  │
  ├── code !== 200 OR empty data → return null
  │
  ├── $tracking = $data['data'][0]  ← First tracking entry
  │
  ├── CjOrder::update(['status' => $tracking['orderStatus']])
  │
  ├── DB::table('shipments')->where('order_id', $orderId)->first()
  │     ← Not found → return null (no shipment row yet)
  │
  └── DB::table('shipments')->update([
        tracking_number → $tracking['trackingNumber'] ?? null
        carrier         → $tracking['carrierName'] ?? 'CJPacket'
        status          → 'delivered' if orderStatus==='delivered', else 'shipped'
        updated_at      → now()
      ])
```

## Path 2: Batch Sync — syncAllActiveShipments()

Fetches all `CjOrder` records **not** in `['delivered', 'cancelled']` and calls `syncShipment()` for each. Errors are silently swallowed (no logging in current impl — ⚠️ debt).

## Path 3: Webhook — handleWebhook(array $payload)

CJ pushes status updates via webhook. Expected payload fields:

| Field | Description |
|---|---|
| `orderNumber` | Our `orders.order_number` |
| `orderStatus` | CJ status string: `shipped`, `delivered`, `cancelled` |
| `trackingNumber` | Carrier tracking number |
| `carrierName` | e.g. `CJPacket`, `YunExpress` |
| `trackingUrl` | Optional tracking URL |

```
handleWebhook($payload)
  │
  ├── Order::where('order_number', $orderNumber)->first()
  │     ← Not found → return silently
  │
  ├── CjOrder::where('internal_order_id', $order->id)->first()
  │     ← Not found → return silently
  │
  ├── CjOrder::update(['status' => $orderStatus])
  │
  ├── trackingNumber present?
  │     └── DB::table('shipments')->update([
  │           tracking_number, carrier,
  │           status → 'delivered' or 'shipped',
  │           updated_at
  │         ])
  │
  └── Status map → update orders.status:
        'shipped'   → 'shipped'
        'delivered' → 'delivered'
        'cancelled' → 'cancelled'
```

## Schema Decision: No `cj_shipments` Table

Both `syncShipment()` and `handleWebhook()` contain the comment:
> `// Removed insertion into cj_shipments as the table does not exist in schema`

Tracking data writes directly to the `shipments` table. The CJ-specific carrier name defaults to `'CJPacket'` if CJ doesn't return one.

## Known Gaps (⚠️ Debt)

1. **`syncAllActiveShipments()` silently eats exceptions** — no logging on individual sync failures.
2. **Webhook route not verified** — no HMAC signature validation on incoming CJ webhooks (security gap).
3. **Only first tracking entry used** — `$data['data'][0]` — if CJ returns multiple shipments for split orders, others are ignored.
4. **`shippedAt` parsed but not stored** — `Carbon::parse($tracking['shippedAt'])` is computed but never written to DB.

## Related
- [[CJ-Dropshipping-MOC]]
- [[CJ-Order-Placement-Flow]]
- [[CJ-Auth-Token-Flow]]
- `../../app/Services/Cj/CjShipmentService.php`

## References
- CJ Logistics API: `GET /logistic/order/list`
