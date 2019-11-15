<?php

namespace MageSuite\OrderFailureNotification\Observer;

class QuoteSubmitFailure implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\OrderFailureNotification\Service\ConfigProviderInterface
     */
    protected $config;

    /**
     * @var \MageSuite\OrderFailureNotification\Service\NotificationInterface
     */
    protected $notification;

    /**
     * @param \MageSuite\OrderFailureNotification\Service\ConfigProviderInterface $config
     * @param \MageSuite\OrderFailureNotification\Service\NotificationInterface $notification
     */
    public function __construct(
        \MageSuite\OrderFailureNotification\Service\ConfigProviderInterface $config,
        \MageSuite\OrderFailureNotification\Service\NotificationInterface $notification
    ) {
        $this->config = $config;
        $this->notification = $notification;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->config->isNotificationEnabled()) {
            $this->notification->orderFailure(
                $this->config->getNotificationRecipients(),
                $observer->getEvent()->getException(),
                $observer->getEvent()->getQuote(),
                $observer->getEvent()->getOrder()
            );
        }
    }
}
