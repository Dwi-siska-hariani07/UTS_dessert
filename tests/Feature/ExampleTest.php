<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test public shop catalog is accessible.
     *
     * @return void
     */
    public function test_shop_page_is_accessible()
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
    }
}
