<?php

namespace Concrete\Package\NotificationBannerKit\Controller\SinglePage\Dashboard\NotificationBanner;

use NotificationBannerKit\Entity\Notification;
use Concrete\Core\Page\Controller\DashboardPageController;
use NotificationBannerKit\Entity\Settings as SettingsEntity;
use NotificationBannerKit\Search\ItemList\Notifications as NotificationList;

class Settings extends DashboardPageController
{
    public $helpers = [
        'form',
        'concrete/ui',
        'text',
    ];

    protected function getNotifications(string $style, SettingsEntity $settings)
    {
        $notifications = [
            '' => t('Choose type...'),
        ];

        $currentId = $settings->getNotification($style)?->getID();

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
        $settings = SettingsEntity::get();

        $this->set('hasNotifications', $this->hasNotifications());
        $this->set('settings', $settings);
        $this->set('bannerNotifications', $this->getNotifications('banner', $settings));
        $this->set('popupNotifications', $this->getNotifications('popup', $settings));
    }

    public function save()
    {
        $this->set('hasNotifications', $this->hasNotifications());
        $this->set('settings', SettingsEntity::get());

        if ($this->post()) {
            if (!$this->hasNotifications()) {
                $this->flash('error', t('Create a notification before changing these settings.'));
                return $this->buildRedirect('/dashboard/notification_banner/settings')->send();
            }

            $settings = SettingsEntity::get();
            $settings->setActive($this->post('activate') !== null);
            $settings->setUseCustomCss($this->post('use_custom_css') !== null);
            $settings->setCanDismiss($this->post('can_dismiss') !== null);

            foreach (['banner', 'popup'] as $style) {
                $id = (string) $this->post('notification_' . $style);
                $notification = $id !== '' ? Notification::getByID($id) : false;
                $settings->setNotification($style, ($notification && $notification->getStyle() === $style) ? $notification : null);
            }

            $this->entityManager->persist($settings);
            $this->entityManager->flush();

            $this->flash('success', t('Notification settings saved.'));
            return $this->buildRedirect('/dashboard/notification_banner/settings')->send();
        }
    }
}
