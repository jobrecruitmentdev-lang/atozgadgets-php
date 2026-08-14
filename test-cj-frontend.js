const { chromium } = require('playwright');

(async () => {
  console.log('👿 Starting /devil E2E Frontend Check on atozgadgetz.com...');
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  // 1. Intercept API calls to check for CJ data leaking or structure
  page.on('response', async response => {
    if (response.url().includes('/api/products') && response.status() === 200) {
      try {
        const json = await response.json();
        console.log(`\n📦 Intercepted API response for Products:`);
        const cjProducts = json.data.products.filter(p => p.fulfillment_type === 'cj');
        console.log(`✅ Found ${cjProducts.length} CJ Products in the API response.`);
        if (cjProducts.length > 0) {
            console.log(`🔍 Sample CJ Product: "${cjProducts[0].name}" (SKU: ${cjProducts[0].sku})`);
        }
      } catch (e) {
        // Ignore non-json
      }
    }
  });

  try {
    // 2. Navigate to Shop page
    console.log('\n🌐 Navigating to /shop...');
    await page.goto('https://atozgadgetz.com/shop', { waitUntil: 'networkidle' });
    
    console.log('✅ Page loaded successfully (No 500 crashes).');
    
    // 3. Devil Check: Try to find product cards
    const productsCount = await page.locator('.product-card, .product-item, article, [data-product-id]').count();
    console.log(`🛒 Found ${productsCount} product items on the frontend UI.`);
    
    if (productsCount === 0) {
       console.log('⚠️ WARNING: No products found on the frontend! Is the UI properly reading the CJ products?');
    }

    // 4. Devil Check: Are CJ images loading?
    const images = await page.evaluate(() => {
        return Array.from(document.querySelectorAll('img'))
                    .map(img => img.src)
                    .filter(src => src.includes('cjdropshipping.com'));
    });
    
    if (images.length > 0) {
        console.log(`✅ SUCCESS: Found ${images.length} CJ Dropshipping images rendering on the frontend!`);
        console.log(`🖼️ Sample Image: ${images[0]}`);
    } else {
        console.log('⚠️ WARNING: No CJ images found rendering on the page.');
    }

  } catch (error) {
    console.error('❌ E2E TEST FAILED:', error);
  } finally {
    await browser.close();
    console.log('\n👿 /devil E2E Check Complete.');
  }
})();
