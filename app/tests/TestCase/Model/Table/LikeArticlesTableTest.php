<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LikeArticlesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LikeArticlesTable Test Case
 */
class LikeArticlesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LikeArticlesTable
     */
    protected $LikeArticles;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.LikeArticles',
        'app.Users',
        'app.Articles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('LikeArticles') ? [] : ['className' => LikeArticlesTable::class];
        $this->LikeArticles = $this->getTableLocator()->get('LikeArticles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->LikeArticles);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LikeArticlesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\LikeArticlesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
