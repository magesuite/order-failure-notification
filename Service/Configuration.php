<?php

namespace MageSuite\OrderFailureNotification\Service;

class Configuration implements ConfigProviderInterface
{
    const XML_PATH_NOTIFICATION_ENABLED = 'order_failure_notification/general/enabled';
    const XML_PATH_NOTIFICATION_EMAIL_ADDRESS = 'order_failure_notification/general/email_address';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isNotificationEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_NOTIFICATION_ENABLED);
    }

    /**
     * @return string[]
     */
    public function getNotificationRecipients()
    {
        $recipients = array_map(
            'trim',
            explode(',', $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_EMAIL_ADDRESS))
        );

        $recipients = array_filter($recipients, function ($recipient) {
            return !!$recipient;
        });

        return $recipients;
    }
}
