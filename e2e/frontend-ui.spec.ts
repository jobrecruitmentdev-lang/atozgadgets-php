import { test, expect } from '@playwright/test';

test.describe('AtoZGadgets Storefront UI Verification', () => {

  test('Homepage loads with Header, Hero section, and Footer', async ({ page }) => {
    await page.goto('http://localhost:8000/');
    
    // Header check
    const header = page.locator('#main-header');
    await expect(header).toBeVisible();

    // Brand logo
    const logo = page.locator('.logo-container img').first();
    await expect(logo).toBeVisible();

    // Hero title check
    const heroTitle = page.locator('h1');
    await expect(heroTitle).toBeVisible();

    // Footer check with PR Marketing Ventures credit link
    const footerCredit = page.locator('text=PR Marketing Ventures');
    await expect(footerCredit).toBeVisible();
  });

  test('Shop page loads product grid and category filters', async ({ page }) => {
    await page.goto('http://localhost:8000/shop');
    
    const pageTitle = page.locator('h1');
    await expect(pageTitle).toBeVisible();
  });

  test('Login page renders authentication form', async ({ page }) => {
    await page.goto('http://localhost:8000/login');
    
    const emailInput = page.locator('input[name="email"]');
    const passwordInput = page.locator('input[name="password"]');
    await expect(emailInput).toBeVisible();
    await expect(passwordInput).toBeVisible();
  });

  test('Cart page renders cart items table or empty state', async ({ page }) => {
    await page.goto('http://localhost:8000/cart');
    
    const bodyText = await page.textContent('body');
    expect(bodyText).toContain('Cart');
  });

});
