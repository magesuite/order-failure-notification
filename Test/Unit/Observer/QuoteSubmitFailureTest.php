<?php

namespace MageSuite\OrderFailureNotification\Test\Unit\Observer;

use \MageSuite\OrderFailureNotification\Observer\QuoteSubmitFailure;

class QuoteSubmitFailureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QuoteSubmitFailure
     */
    private $instance;

    protected function setUp()
    {
        /** @var \Magento\Framework\Mail\Template\TransportBuilder|\PHPUnit\Framework\MockObject\MockObject $transportBuilderMock */
        $transportBuilderMock = $this->getMockBuilder(\Magento\Framework\Mail\Template\TransportBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject $scopeConfigMock */
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject $storeManagerMock */
        $storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = new QuoteSubmitFailure(
            $transportBuilderMock,
            $scopeConfigMock,
            $storeManagerMock,
            $loggerMock
        );
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(QuoteSubmitFailure::class, $this->instance);
    }

    public function testItImplementsObserverInterface()
    {
        $this->assertInstanceOf(\Magento\Framework\Event\ObserverInterface::class, $this->instance);
    }
}
