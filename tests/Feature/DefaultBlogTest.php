<?php

namespace Bjuppa\LaravelBlog\Tests\Feature;

use Bjuppa\LaravelBlog\Tests\IntegrationTest;
use Bjuppa\LaravelBlog\Contracts\BlogRegistry as BlogRegistryContract;
use Illuminate\Support\Facades\Route;

class DefaultBlogTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
        $this->loadRegisteredMigrations();
        $this->seedDefaultBlogEntry();
    }

    public function test_blog_is_configured()
    {
        $default_registered_blog = $this->app->make(BlogRegistryContract::class)->get('default');

        $this->assertEquals(config('blog.blogs.default.public_path'), $default_registered_blog->getPublicPath());
        $this->assertInstanceOf(config('blog.blogs.default.entry_provider'), $default_registered_blog->getEntryProvider());
    }

    public function test_blog_has_named_routes()
    {
        $this->assertTrue(Route::has('blog.default.index'));
        $this->assertTrue(Route::has('blog.default.entry'));
    }

    public function test_index_page()
    {
        $response = $this->get('blog');

        $response->assertStatus(200);
    }

    public function test_entry_page() {
        $response = $this->get('blog/the-first-post');

        $response->assertStatus(200);
    }

    public function test_entry_not_found() {
        $response = $this->get('blog/non-existing-entry');

        $response->assertStatus(404);
    }
}