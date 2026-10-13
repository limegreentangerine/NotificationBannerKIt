<?php

namespace NotificationBannerKit\Events;

use Concrete\Core\Page\Page;
use Concrete\Core\View\View;
use NotificationBannerKit\Entity\Settings;
use Concrete\Core\Localization\Localization;

class NotificationBanner
{
    /**
     * Null if the settings can't be read, eg. before the package upgrade that creates the table has been run,
     * so a missing table can't take every page down.
     */
    protected static function getSettings(): ?Settings
    {
        try {
            return Settings::get();
        } catch (\Throwable) {
            return null;
        }
    }
    public static function onBeforeRender()
    {
        $page = Page::getCurrentPage();
        $settings = self::getSettings();
        $display = false;

        if ($settings && $page) {
            if ($settings->isActive()) {
                $display = match (true) {
                    $page->isAdminArea(),
                    $page->getCollectionPath() == '/login',
                    $page->getCollectionPath() == '/register',
                    $page->getCollectionPath() == '/account/two_factor_auth' => false,
                    default => true
                };
            }

            if ($display) {
                $locale = Localization::activeLanguage();
                $html = \Core::make('helper/html');
                $useCustomCss = $settings->usesCustomCss();
                $output = '';

                foreach (['banner', 'popup'] as $style) {
                    $notification = $settings->getNotification($style);

                    if (!$notification || $notification->getStyle() !== $style || !$notification->isWithinSchedule() || !$notification->isVisibleOnPage($page)) {
                        continue;
                    }

                    ob_start();
                    echo '<component-notification>';
                    View::element(sprintf('notification_%s', $style), [
                        'id' => $notification->getID(),
                        'version' => $notification->getDateUpdated()->getTimestamp(),
                        'style' => $style,
                        'notification' => $notification->getBanner($locale),
                        'dismissable' => ($style === 'popup') ? true : $settings->canDismiss(),
                    ], 'notification_banner_kit');
                    echo '</component-notification>';
                    $output .= ob_get_contents();
                    ob_end_clean();

                    if (!$useCustomCss) {
                        $page->getPageController()->addHeaderItem($html->css(sprintf('notification-%s.css', $style), 'notification_banner_kit'));
                    }
                }

                if ($output !== '') {
                    if (!$useCustomCss) {
                        $page->getPageController()->addHeaderItem($html->css('notification-global.css', 'notification_banner_kit'));
                    }
                    $page->getPageController()->addFooterItem($output);
                    $page->getPageController()->addFooterItem($html->javascript('notification-banner.js', 'notification_banner_kit'));
                }
            }
        }
    }
}
