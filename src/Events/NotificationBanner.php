<?php

namespace NotificationBannerKit\Events;

use Concrete\Core\Page\Page;
use Concrete\Core\View\View;
use Concrete\Core\Package\Package;
use Concrete\Core\Localization\Localization;

class NotificationBanner
{
    public static function onBeforeRender()
    {
        $page = Page::getCurrentPage();
        $pkg = Package::getByHandle('notification_banner_kit')?->getController();
        $display = false;

        if ($pkg && $page) {
            if ($pkg->getFileConfig()->get('notification_banner.activate') === true) {
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
                $useCustomCss = $pkg->getFileConfig()->get('notification_banner.use_custom_css') === true;
                $output = '';

                foreach (['banner', 'popup'] as $style) {
                    $configId = $pkg->getFileConfig()->get('notification_banner.notification_' . $style);
                    $notification = $configId ? \NotificationBannerKit\Entity\Notification::getByID($configId) : false;

                    if (!$notification || $notification->getStyle() !== $style || !$notification->isWithinSchedule() || !$notification->isVisibleOnPage($page)) {
                        continue;
                    }

                    ob_start();
                    $view = new View();
                    echo '<component-notification>';
                    $view->element(sprintf('notification_%s', $style), [
                        'id' => $notification->getID(),
                        'style' => $style,
                        'notification' => $notification->getBanner($locale),
                        'dismissable' => ($style === 'popup') ? true : $pkg->getFileConfig()->get('notification_banner.can_dismiss'),
                    ], 'notification_banner_kit');
                    echo '</component-notification>';
                    $output .= ob_get_contents();
                    ob_end_clean();

                    if (!$useCustomCss) {
                        $page->getController()->addHeaderItem($html->css(sprintf('notification-%s.css', $style), 'notification_banner_kit'));
                    }
                }

                if ($output !== '') {
                    if (!$useCustomCss) {
                        $page->getController()->addHeaderItem($html->css('notification-global.css', 'notification_banner_kit'));
                    }
                    $page->getController()->addFooterItem($output);
                    $page->getController()->addFooterItem($html->javascript('notification-banner.js', 'notification_banner_kit'));
                }
            }
        }
    }
}
