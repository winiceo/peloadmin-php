<?php



namespace Leven\Tests\Unit\Packages\Wallet;

use Leven\Tests\TestCase;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\TargetTypeManager;
use Leven\Models\WalletOrder as WalletOrderModel;
use Leven\Packages\Wallet\TargetTypes\UserTarget;

class TargetTypeManagerTest extends TestCase
{
    /**
     * Test TargetTypeManager.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testBaseClass()
    {
        // Create a WalletOrderModel::class
        $model = new WalletOrderModel();

        // Create a Order::class mock.
        $order = $this->getMockBuilder(Order::class)
                      ->setMethods(['getOrderModel'])
                      ->getMock();
        $order->expects($this->exactly(1))
              ->method('getOrderModel')
              ->willReturn($model);

        // Create a TargetTypeManager::class
        $targetTypeManager = new TargetTypeManager($this->app);
        $targetTypeManager->setOrder($order);

        // dd(Order::TARGET_TYPE_USER);

        // test getDefaultDriver.
        $model->target_type = Order::TARGET_TYPE_USER;
        $this->assertSame(Order::TARGET_TYPE_USER, $targetTypeManager->getDefaultDriver());

        // test Order::TARGET_TYPE_USER Driver instance of.
        $this->assertInstanceOf(UserTarget::class, $targetTypeManager->driver(Order::TARGET_TYPE_USER));
    }
}
