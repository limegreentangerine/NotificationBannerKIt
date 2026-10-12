<?php

namespace NotificationBannerKit\Log;

use ClassKit\Log\Logger;

class NotificationBannerLogger extends Logger
{
    public function __construct()
    {
        parent::__construct('notification_banner');
    }
}
