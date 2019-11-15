<?php

namespace MageSuite\OrderFailureNotification\Test\Integration\Observer;

class QuoteSubmitFailureTest extends \PHPUnit\Framework\TestCase
{
    const EVENT_NAME = 'sales_model_service_quote_submit_failure';
    const OBSERVER_NAME = 'quote_submit_failure_notification';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Event\ConfigInterface
     */
    protected $eventConfig;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->eventConfig = $this->objectManager->create(\Magento\Framework\Event\ConfigInterface::class);
        $this->eventManager = $this->objectManager->create(\Magento\Framework\Event\ManagerInterface::class);
    }

    /**
     * @param string $event
     * @return array
     */
    protected function getObservers($event)
    {
        return $this->eventConfig->getObservers($event);
    }

    public function testItIsConfiguredToObserveQuoteSubmitFailureEvent()
    {
        $observers = $this->eventConfig->getObservers(self::EVENT_NAME);

        $this->assertArrayHasKey(
            self::OBSERVER_NAME,
            $observers,
            sprintf('\'%s\' observer is not bound to \'%s\' event', self::OBSERVER_NAME, self::EVENT_NAME)
        );

        $this->assertSame(
            \MageSuite\OrderFailureNotification\Observer\QuoteSubmitFailure::class,
            $observers[self::OBSERVER_NAME]['instance']
        );
    }
}
