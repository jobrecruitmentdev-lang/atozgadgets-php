import { test, expect, APIRequestContext } from '@playwright/test';

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

  test.describe('Protected Admin CJ Dropshipping Routes', () => {
    let authContext: APIRequestContext;
    let importedProductId: number | null = null;

    test.beforeAll(async ({ playwright, request }) => {
      // 1. Skip if no admin credentials are provided in env (prevents failure on live server without credentials)
      if (!process.env.ADMIN_EMAIL || !process.env.ADMIN_PASSWORD) {
        test.skip(true, 'ADMIN_EMAIL and ADMIN_PASSWORD env vars are required for live admin tests.');
      }

      // 2. Perform Login to get session cookies
      const loginRes = await request.post('/login', {
        form: {
          email: process.env.ADMIN_EMAIL,
          password: process.env.ADMIN_PASSWORD
        }
      });
      expect(loginRes.status()).toBe(200); // Or 302 depending on controller

      // 3. Create a new context with the cookies from the login response
      const storageState = await request.storageState();
      authContext = await playwright.request.newContext({ storageState });
    });

    test('GET /admin/api/catalog/search returns CJ catalog search results', async () => {
      if(!authContext) return;
      const response = await authContext.get('/admin/api/catalog/search?keyword=drone');
      expect(response.status()).toBe(200);
      const data = await response.json();
      expect(data.data.list.length).toBeGreaterThan(0);
    });

    test('POST /admin/api/catalog/import-item imports CJ product safely', async () => {
      if(!authContext) return;
      const response = await authContext.post('/admin/api/catalog/import-item', {
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
      
      // Save ID for cleanup
      if (data.internal_id) {
        importedProductId = data.internal_id;
      }
    });

    test.afterAll(async () => {
      // Cleanup: Delete the junk product from the live database
      if (importedProductId && authContext) {
        const delRes = await authContext.delete(`/admin/catalog/products/${importedProductId}`);
        expect([200, 302]).toContain(delRes.status());
      }
    });
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

  test('POST /payment/paypal/create-order & capture-order validate session and payload', async ({ request }) => {
    // 1. Create order should fail with 400 because there is no session cart in this stateless API test
    const createRes = await request.post('/payment/paypal/create-order');
    expect(createRes.status()).toBe(400);
    const createData = await createRes.json();
    expect(createData.error).toBe('Cart is empty');

    // 2. Capture order should fail with 422 (or 302 redirect depending on Laravel's Accepts header) or 500/400 because paypal_order_id is missing
    const captureRes = await request.post('/payment/paypal/capture-order', {
      headers: { 'Accept': 'application/json' }
    });
    expect(captureRes.status()).toBe(422);
    const captureData = await captureRes.json();
    expect(captureData.errors).toHaveProperty('paypal_order_id');
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
