<?php

declare(strict_types=1);

namespace MageSuite\OrderFailureNotification\Service;

class Configuration implements ConfigProviderInterface
{
    public const XML_PATH_NOTIFICATION_ENABLED = 'order_failure_notification/general/enabled';
    public const XML_PATH_NOTIFICATION_EMAIL_ADDRESS = 'order_failure_notification/general/email_address';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isNotificationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_NOTIFICATION_ENABLED);
    }

    /**
     * @return string[]
     */
    public function getNotificationRecipients(): array
    {
        $emails = $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_EMAIL_ADDRESS);

        if (!$emails) {
            return [];
        }

        $recipients = array_map(
            'trim',
            explode(',', $emails)
        );

        return array_filter($recipients, function ($recipient) {
            return !!$recipient;
        });
    }
}
