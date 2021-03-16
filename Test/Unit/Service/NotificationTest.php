<?php

namespace MageSuite\OrderFailureNotification\Test\Unit\Service;

class NotificationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\OrderFailureNotification\Service\Notification
     */
    protected $instance;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transportBuilderMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    protected function setUp(): void
    {
        $this->transportBuilderMock = $this->getMockBuilder(\Magento\Framework\Mail\Template\TransportBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builderFluentMethods = [
            'setTemplateIdentifier',
            'setTemplateOptions',
            'setTemplateVars',
            'setFromByScope',
            'addTo'
        ];

        foreach ($builderFluentMethods as $method) {
            $this->transportBuilderMock
                ->method($method)
                ->willReturn($this->transportBuilderMock);
        }

        /**
         * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject $storeManagerMock
         */
        $storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Store\Api\Data\StoreInterface|\PHPUnit\Framework\MockObject\MockObject $storeMock */
        $storeMock = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)->getMock();
        $storeMock->method('getId')->willReturn(1);
        $storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = new \MageSuite\OrderFailureNotification\Service\Notification(
            $this->transportBuilderMock,
            $storeManagerMock,
            $this->loggerMock
        );
    }

    /**
     * @return \Magento\Framework\Mail\TransportInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMailTransportMock()
    {
        /** @var \Magento\Framework\Mail\TransportInterface|\PHPUnit\Framework\MockObject\MockObject $transportMock */
        $transportMock = $this->getMockBuilder(\Magento\Framework\Mail\TransportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transportBuilderMock
            ->method('getTransport')
            ->willReturn($transportMock);

        return $transportMock;
    }

    /**
     * @return \Magento\Quote\Model\Quote|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getQuoteMock()
    {
        /** @var \Magento\Quote\Model\Quote|\PHPUnit\Framework\MockObject\MockObject $quoteMock */
        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $quoteMock;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getOrderMock()
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface|\PHPUnit\Framework\MockObject\MockObject $orderMock */
        $orderMock = $this->getMockBuilder(\Magento\Sales\Api\Data\OrderInterface::class)->getMock();
        return $orderMock;
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(\MageSuite\OrderFailureNotification\Service\Notification::class, $this->instance);
    }

    public function testItImplementsNotificationInterface()
    {
        $this->assertInstanceOf(
            \MageSuite\OrderFailureNotification\Service\NotificationInterface::class,
            $this->instance
        );
    }

    public function testOrderFailureMethodSendsEmail()
    {
        $this->getMailTransportMock()
            ->expects(self::once())
            ->method('sendMessage');

        $this->instance->orderFailure(
            ['john.doe@example.com'],
            new \Exception(),
            $this->getQuoteMock(),
            $this->getOrderMock()
        );
    }

    public function testOrderFailureMethodLogsErrorOnException()
    {
        $exceptionMessage = 'Error';

        $this->getMailTransportMock()
            ->method('sendMessage')
            ->willThrowException(new \Exception($exceptionMessage));

        $this->loggerMock
            ->expects(self::once())
            ->method('error')
            ->with($exceptionMessage);

        $this->instance->orderFailure(
            ['john.doe@example.com'],
            new \Exception(),
            $this->getQuoteMock(),
            $this->getOrderMock()
        );
    }
}
