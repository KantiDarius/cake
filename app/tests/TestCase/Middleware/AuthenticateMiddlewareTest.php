<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use App\Middleware\AuthenticateMiddleware;
use Cake\TestSuite\TestCase;

/**
 * App\Middleware\AuthenticateMiddleware Test Case
 */
class AuthenticateMiddlewareTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Middleware\AuthenticateMiddleware
     */
    protected $Authenticate;

    /**
     * Test process method
     *
     * @return void
     * @uses \App\Middleware\AuthenticateMiddleware::process()
     */
    public function testProcess(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
