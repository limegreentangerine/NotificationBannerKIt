<?php

namespace Concrete\Package\NotificationBannerKit\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class NotificationBanner extends DashboardPageController
{
    public function view()
    {
        $this->buildRedirect('/dashboard/notification_banner/notifications')->send();
    }
}
