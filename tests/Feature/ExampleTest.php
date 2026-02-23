<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_get_started_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Early Brain Tumor Insights for Clinical Teams');
    }

    public function test_the_application_returns_about_page(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('About The Brain Tumor Detection Project');
    }
}
