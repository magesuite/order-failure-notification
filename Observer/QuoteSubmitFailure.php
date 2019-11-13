<?php
/**
 * Order failures notifications
 *
 * @copyright 2019 creativestyle
 */

namespace MageSuite\OrderFailureNotification\Observer;

class QuoteSubmitFailure implements \Magento\Framework\Event\ObserverInterface
{
    const EMAIL_TEMPLATE = 'order_failure_notification_email_template';
    const XML_PATH_NOTIFICATION_ENABLED = 'order_failure_notification/general/enabled';
    const XML_PATH_NOTIFICATION_EMAIL_ADDRESS = 'order_failure_notification/general/email_address';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_ENABLED)) {
            try {
                $recipients = explode(',', $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_EMAIL_ADDRESS));

                $transport = $this->transportBuilder->setTemplateIdentifier(self::EMAIL_TEMPLATE)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                            'store' => $this->getStoreId()
                        ]
                    )
                    ->setTemplateVars(
                        [
                            'quote' => $observer->getEvent()->getQuote(),
                            'order' => $observer->getEvent()->getOrder(),
                            'exception' => $observer->getEvent()->getException()
                        ]
                    )
                    ->setFromByScope('general', $this->getStoreId())
                    ->addTo($recipients)
                    ->getTransport();

                $transport->sendMessage();
            } catch (\Exception $exception) {
                $this->logger->warning($exception);
            }
        }
    }
}
