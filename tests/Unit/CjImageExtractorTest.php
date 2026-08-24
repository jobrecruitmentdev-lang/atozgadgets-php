<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Cj\CjProductService;

class CjImageExtractorTest extends TestCase
{
    /** @test */
    public function it_extracts_images_from_json_string_array()
    {
        $raw = '["https://cf.cjdropshipping.com/1.jpg", "https://cf.cjdropshipping.com/2.jpg", "https://cf.cjdropshipping.com/3.jpg"]';
        $result = CjProductService::extractImageList($raw);

        $this->assertCount(3, $result);
        $this->assertEquals('https://cf.cjdropshipping.com/1.jpg', $result[0]);
        $this->assertEquals('https://cf.cjdropshipping.com/2.jpg', $result[1]);
        $this->assertEquals('https://cf.cjdropshipping.com/3.jpg', $result[2]);
    }

    /** @test */
    public function it_extracts_images_from_comma_separated_string()
    {
        $raw = 'https://cf.cjdropshipping.com/a.jpg, https://cf.cjdropshipping.com/b.jpg, https://cf.cjdropshipping.com/c.jpg';
        $result = CjProductService::extractImageList($raw);

        $this->assertCount(3, $result);
        $this->assertEquals('https://cf.cjdropshipping.com/a.jpg', $result[0]);
        $this->assertEquals('https://cf.cjdropshipping.com/b.jpg', $result[1]);
    }

    /** @test */
    public function it_extracts_images_from_native_php_array()
    {
        $raw = [
            'https://cf.cjdropshipping.com/x.jpg',
            'https://cf.cjdropshipping.com/y.jpg'
        ];
        $result = CjProductService::extractImageList($raw);

        $this->assertCount(2, $result);
        $this->assertEquals('https://cf.cjdropshipping.com/x.jpg', $result[0]);
    }

    /** @test */
    public function it_handles_empty_and_corrupted_inputs_cleanly()
    {
        $this->assertEmpty(CjProductService::extractImageList(null));
        $this->assertEmpty(CjProductService::extractImageList(''));
        $this->assertEmpty(CjProductService::extractImageList('   '));
        $this->assertEmpty(CjProductService::extractImageList('invalid text without url'));
    }

    /** @test */
    public function it_deduplicates_images_and_preserves_order()
    {
        $raw = '["https://cf.cjdropshipping.com/1.jpg", "https://cf.cjdropshipping.com/2.jpg", "https://cf.cjdropshipping.com/1.jpg"]';
        $result = CjProductService::extractImageList($raw);

        $this->assertCount(2, $result);
        $this->assertEquals(['https://cf.cjdropshipping.com/1.jpg', 'https://cf.cjdropshipping.com/2.jpg'], $result);
    }
}
