<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The header is one floating pill: menus grow out of it, it condenses on
 * scroll, and every menu item still works as a plain link without script.
 */
class SiteHeaderTest extends TestCase
{
    public function test_every_menu_trigger_has_its_panel_and_a_real_link(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        foreach (config('company.nav') as $item) {
            $this->assertStringContainsString('href="'.$item['href'].'"', $content);

            if (isset($item['menu'])) {
                $this->assertStringContainsString('aria-controls="menu-'.$item['menu'].'"', $content);
                $this->assertStringContainsString('id="menu-'.$item['menu'].'"', $content);
            }
        }
    }

    public function test_the_loans_menu_lists_every_product_with_a_small_thumbnail(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $menu = substr($content, strpos($content, 'id="menu-loans"'));
        $menu = substr($menu, 0, strpos($menu, 'id="menu-about"'));

        foreach (config('marketing.products') as $product) {
            $this->assertStringContainsString(e($product['name']), $menu);
            $this->assertStringContainsString(str_replace('-1600.webp', '-480.webp', $product['image']['src']), $menu);
            $this->assertFileExists(public_path(str_replace('-1600.webp', '-480.webp', $product['image']['src'])));
        }
    }

    public function test_the_homepage_header_starts_over_the_photograph_and_other_pages_reserve_its_space(): void
    {
        $home = $this->get('/')->assertOk()->getContent();
        $insights = $this->get(route('insights.index'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<header[^>]*data-over-photo/', $home);
        $this->assertDoesNotMatchRegularExpression('/<header[^>]*data-over-photo(?!=)/', $insights);
        $this->assertStringContainsString('class="h-22 sm:h-26 lg:h-28"', $insights);
        $this->assertStringNotContainsString('class="h-22 sm:h-26 lg:h-28"', $home);
    }

    public function test_the_mobile_menu_is_wired(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-controls="mobile-menu"', false)
            ->assertSee('id="mobile-menu"', false);
    }
}
