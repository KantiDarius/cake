<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\UsersComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\UsersComponent Test Case
 */
class UsersComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\UsersComponent
     */
    protected $Users;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Users = new UsersComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }
}
