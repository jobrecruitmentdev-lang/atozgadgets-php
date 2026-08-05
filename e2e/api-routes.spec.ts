import { test, expect } from '@playwright/test';

test.describe('AtoZGadgets PHP API E2E Test Suite', () => {

  test('GET /health returns 200 ok status', async ({ request }) => {
    const response = await request.get('/health');
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data.status).toBe('ok');
  });

  test('GET /api/products returns products list', async ({ request }) => {
    const response = await request.get('/api/products');
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data).toHaveProperty('success', true);
  });

  test('GET /api/categories returns categories list', async ({ request }) => {
    const response = await request.get('/api/categories');
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data).toHaveProperty('success', true);
  });

  test('POST /api/auth/login with invalid credentials returns 401', async ({ request }) => {
    const response = await request.post('/api/auth/login', {
      data: {
        email: 'invalid-user@example.com',
        password: 'wrongpassword'
      }
    });
    expect(response.status()).toBe(401);
  });

  test('GET /api/admin/cj/search returns CJ catalog search results', async ({ request }) => {
    const response = await request.get('/admin/api/catalog/search?keyword=drone');
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data.data.list.length).toBeGreaterThan(0);
  });

  test('POST /admin/api/catalog/import-item imports CJ product safely', async ({ request }) => {
    const response = await request.post('/admin/api/catalog/import-item', {
      data: {
        pid: 'CJ-PW-TEST-001',
        title: 'Playwright E2E Test Gadget',
        price: 29.99,
        image: 'https://example.com/item.jpg',
        category: 'Electronics'
      }
    });
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data.success).toBe(true);
  });

  test('POST /api/payment/razorpay/create-order returns order payload', async ({ request }) => {
    const response = await request.post('/api/payment/razorpay/create-order', {
      data: { amount: 1500 }
    });
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data.success).toBe(true);
    expect(data.currency).toBe('INR');
  });

  test('POST /api/payment/razorpay/verify returns verified status', async ({ request }) => {
    const response = await request.post('/api/payment/razorpay/verify', {
      data: { razorpay_order_id: 'ord_123', razorpay_payment_id: 'pay_123' }
    });
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data.status).toBe('verified');
  });

  test('POST /api/payment/paypal/create-order & capture-order', async ({ request }) => {
    const createRes = await request.post('/api/payment/paypal/create-order');
    expect(createRes.status()).toBe(200);
    const createData = await createRes.json();
    expect(createData.status).toBe('CREATED');

    const captureRes = await request.post('/api/payment/paypal/capture-order');
    expect(captureRes.status()).toBe(200);
    const captureData = await captureRes.json();
    expect(captureData.status).toBe('COMPLETED');
  });

  test('POST /api/cj/webhook handles external webhook notification', async ({ request }) => {
    const response = await request.post('/api/cj/webhook', {
      data: {
        event: 'order.status_update',
        cj_order_id: 'CJ-ORD-TEST123',
        status: 'shipped'
      }
    });
    expect(response.status()).toBe(200);
    const data = await response.json();
    expect(data.success).toBe(true);
  });

});
