<?php

namespace MageSuite\OrderFailureNotification\Service;

interface NotificationInterface
{
    /**
     * @param array $recipients
     * @param \Exception $exception
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function orderFailure($recipients, $exception, $quote, $order);
}
