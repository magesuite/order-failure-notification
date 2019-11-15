<?php

namespace MageSuite\OrderFailureNotification\Test\Unit\Observer;

class QuoteSubmitFailureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\OrderFailureNotification\Observer\QuoteSubmitFailure
     */
    protected $instance;

    /**
     * @var \MageSuite\OrderFailureNotification\Service\ConfigProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \MageSuite\OrderFailureNotification\Service\NotificationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationMock;

    protected function setUp()
    {
        $this->configMock = $this->getMockBuilder(
            \MageSuite\OrderFailureNotification\Service\ConfigProviderInterface::class
        )->getMock();

        $this->notificationMock = $this->getMockBuilder(
            \MageSuite\OrderFailureNotification\Service\NotificationInterface::class
        )->getMock();

        $this->instance = new \MageSuite\OrderFailureNotification\Observer\QuoteSubmitFailure(
            $this->configMock,
            $this->notificationMock
        );
    }

    /**
     * @return \Magento\Framework\Event\Observer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getObserverMock()
    {
        $eventMock = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->method('__call')
            ->willReturn(new \stdClass());

        $observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $observerMock->method('getEvent')
            ->willReturn($eventMock);

        return $observerMock;
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(\MageSuite\OrderFailureNotification\Observer\QuoteSubmitFailure::class, $this->instance);
    }

    public function testItImplementsObserverInterface()
    {
        $this->assertInstanceOf(\Magento\Framework\Event\ObserverInterface::class, $this->instance);
    }

    public function testItCallsNotificationOrderFailureMethodWhenNotificationIsEnabled()
    {
        $this->configMock
            ->method('isNotificationEnabled')
            ->willReturn(true);

        $this->notificationMock
            ->expects(self::once())
            ->method('orderFailure');

        $this->instance->execute($this->getObserverMock());
    }

    public function testItDoesNotCallNotificationOrderFailureMethodWhenNotificationIsDisabled()
    {
        $this->configMock
            ->method('isNotificationEnabled')
            ->willReturn(false);

        $this->notificationMock
            ->expects(self::never())
            ->method('orderFailure');

        $this->instance->execute($this->getObserverMock());
    }
}
