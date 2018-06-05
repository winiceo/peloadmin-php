<?php



namespace Leven\Tests\Unit\Packages\Wallet\TargetTypes;

use Leven\Tests\TestCase;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\TargetTypes\Target;

class TargetTest extends TestCase
{
    /**
     * Test target setOrder method.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testSetOrder()
    {
        $target = $this->getMockForAbstractClass(TestTargetClass::class);
        $order = $this->createMock(Order::class);

        $target->setOrder($order);
        $this->assertSame($order, $target->getOrder());
    }
}

abstract class TestTargetClass extends Target
{
    public function getOrder()
    {
        return $this->order;
    }
}
