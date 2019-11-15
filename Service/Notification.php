<?php

namespace MageSuite\OrderFailureNotification\Service;

class Notification implements NotificationInterface
{
    const EMAIL_TEMPLATE = 'order_failure_notification_email_template';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param array $recipients
     * @param \Exception $exception
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function orderFailure($recipients, $exception, $quote, $order)
    {
        try {
            $this->transportBuilder
                ->setTemplateIdentifier(self::EMAIL_TEMPLATE)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                        'store' => $this->getStoreId()
                    ]
                )
                ->setTemplateVars(['exception' => $exception, 'quote' => $quote, 'order' => $order])
                ->setFromByScope('general', $this->getStoreId())
                ->addTo($recipients);

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
