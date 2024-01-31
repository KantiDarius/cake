<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use App\Middleware\UnauthenticatedMiddleware;
use Cake\TestSuite\TestCase;

/**
 * App\Middleware\UnauthenticatedMiddleware Test Case
 */
class UnauthenticatedMiddlewareTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Middleware\UnauthenticatedMiddleware
     */
    protected $Unauthenticated;

    /**
     * Test process method
     *
     * @return void
     * @uses \App\Middleware\UnauthenticatedMiddleware::process()
     */
    public function testProcess(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
