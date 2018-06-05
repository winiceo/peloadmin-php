<?php



namespace Leven\Tests\Unit\Packages\Wallet;

use Leven\Tests\TestCase;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\TypeManager;
use Leven\Packages\Wallet\Types\UserType;

class TypeManagerTest extends TestCase
{
    protected $typeManager;

    /**
     * Setup the test environment.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function setUp()
    {
        parent::setUp();

        $this->typeManager = $this->app->make(TypeManager::class);
    }

    /**
     * Test get default driver return.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testGetDefaultDriver()
    {
        $defaultDriverString = $this->typeManager->getDefaultDriver();
        $this->assertSame(Order::TARGET_TYPE_USER, $defaultDriverString);
    }

    /**
     * Test Create user driver.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testCreateUserDriver()
    {
        $userType = $this->typeManager->driver(Order::TARGET_TYPE_USER);

        $this->assertInstanceOf(UserType::class, $userType);
    }
}
