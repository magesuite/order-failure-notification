<?php

namespace MageSuite\OrderFailureNotification\Service;

interface ConfigProviderInterface
{
    /**
     * @return bool
     */
    public function isNotificationEnabled();

    /**
     * @return string[]
     */
    public function getNotificationRecipients();
}
