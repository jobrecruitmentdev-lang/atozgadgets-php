const http = require('http');

async function request(path, options = {}) {
    return new Promise((resolve, reject) => {
        const req = http.request({
            hostname: '127.0.0.1',
            port: 8000,
            path: path,
            method: options.method || 'GET',
            headers: options.headers || { 'Accept': 'application/json' }
        }, (res) => {
            let body = '';
            res.on('data', chunk => body += chunk);
            res.on('end', () => {
                resolve({ status: res.statusCode, headers: res.headers, body });
            });
        });
        req.on('error', reject);
        if (options.body) {
            req.write(options.body);
        }
        req.end();
    });
}

async function runApiTests() {
    console.log('--- Starting Backend API Test Suite ---');
    let passed = 0;
    let failed = 0;

    // Test 1: Health endpoint
    try {
        const res = await request('/health');
        if (res.status === 200) {
            console.log('✅ GET /health -> 200 OK');
            passed++;
        } else {
            console.error('❌ GET /health failed with status', res.status);
            failed++;
        }
    } catch (e) {
        console.error('❌ GET /health error:', e.message);
        failed++;
    }

    // Test 2: Storefront Home
    try {
        const res = await request('/');
        if (res.status === 200) {
            console.log('✅ GET / -> 200 OK (Storefront Home)');
            passed++;
        } else {
            console.error('❌ GET / failed with status', res.status);
            failed++;
        }
    } catch (e) {
        console.error('❌ GET / error:', e.message);
        failed++;
    }

    // Test 3: Admin Catalog Products List
    try {
        const res = await request('/admin/catalog/products');
        if (res.status === 200) {
            console.log('✅ GET /admin/catalog/products -> 200 OK');
            passed++;
        } else {
            console.error('❌ GET /admin/catalog/products failed with status', res.status);
            failed++;
        }
    } catch (e) {
        console.error('❌ GET /admin/catalog/products error:', e.message);
        failed++;
    }

    // Test 4: Admin Catalog Categories List
    try {
        const res = await request('/admin/catalog/categories');
        if (res.status === 200) {
            console.log('✅ GET /admin/catalog/categories -> 200 OK');
            passed++;
        } else {
            console.error('❌ GET /admin/catalog/categories failed with status', res.status);
            failed++;
        }
    } catch (e) {
        console.error('❌ GET /admin/catalog/categories error:', e.message);
        failed++;
    }

    // Test 5: CJ Catalog API Search
    try {
        const res = await request('/admin/api/catalog/search?keyword=drone');
        if (res.status === 200) {
            console.log('✅ GET /admin/api/catalog/search?keyword=drone -> 200 OK');
            passed++;
        } else {
            console.error('❌ GET /admin/api/catalog/search failed with status', res.status);
            failed++;
        }
    } catch (e) {
        console.error('❌ GET /admin/api/catalog/search error:', e.message);
        failed++;
    }

    console.log(`\n--- Test Summary: ${passed} Passed, ${failed} Failed ---`);
    process.exit(failed > 0 ? 1 : 0);
}

runApiTests();
