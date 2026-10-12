<?php

namespace Concrete\Package\NotificationBannerKit\Controller\SinglePage\Dashboard\NotificationBanner;

use Package;
use Concrete\Core\Page\Controller\DashboardPageController;
use NotificationBannerKit\Search\ItemList\Notifications as NotificationList;

class Settings extends DashboardPageController
{
    public $helpers = [
        'form',
        'concrete/ui',
        'text',
    ];

    protected function getNotifications(string $style)
    {
        $notifications = [
            '' => t('Choose type...'),
        ];

        $pkg = Package::getByHandle('notification_banner_kit');
        $currentId = is_object($pkg) ? $pkg->getFileConfig()->get('notification_banner.notification_' . $style) : null;

        $nl = new NotificationList();
        $nl->filterByStyle($style);
        $nl->sortByDateUpdated('desc');
        $nls = $nl->getResults();

        foreach ($nls as $n) {
            if ($n->isActive() || $n->getID() == $currentId) {
                $notifications[$n->getID()] = $n->getName() . (!$n->isActive() ? ' ' . t('(inactive)') : '');
            }
        }

        return $notifications;
    }

    protected function hasNotifications(): bool
    {
        return (new NotificationList())->getTotalResults() > 0;
    }

    public function view()
    {
        $this->set('hasNotifications', $this->hasNotifications());

        $pkg = Package::getByHandle('notification_banner_kit');
        if (is_object($pkg)) {
            $this->set('pkg', $pkg);
        }

        $this->set('bannerNotifications', $this->getNotifications('banner'));
        $this->set('popupNotifications', $this->getNotifications('popup'));
    }

    public function save()
    {
        $this->set('hasNotifications', $this->hasNotifications());
        if ($this->post()) {
            if (!$this->hasNotifications()) {
                $this->flash('error', t('Create a notification before changing these settings.'));
                return $this->buildRedirect('/dashboard/notification_banner/settings')->send();
            }
            $pkg = Package::getByHandle('notification_banner_kit');
            if (is_object($pkg)) {
                $this->set('pkg', $pkg);
                $config = $pkg->getFileConfig();
                $config->save('notification_banner.activate', $this->post('activate') !== null);
                $config->save('notification_banner.use_custom_css', $this->post('use_custom_css') !== null);
                $config->save('notification_banner.can_dismiss', $this->post('can_dismiss') !== null);
                $config->save('notification_banner.notification_banner', $this->post('notification_banner'));
                $config->save('notification_banner.notification_popup', $this->post('notification_popup'));

                $this->flash('success', t('Notification settings saved.'));
                return $this->buildRedirect('/dashboard/notification_banner/settings')->send();
            }
            $this->set('formContent', $this->post());

        }
    }
}
