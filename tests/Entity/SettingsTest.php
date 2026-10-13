<?php

declare(strict_types=1);

namespace NotificationBannerKit\Tests\Entity;

use PHPUnit\Framework\TestCase;
use NotificationBannerKit\Entity\Settings;
use NotificationBannerKit\Entity\Notification;

final class SettingsTest extends TestCase
{
    public function testEverythingIsOffByDefault(): void
    {
        $settings = new Settings();

        $this->assertFalse($settings->isActive());
        $this->assertFalse($settings->usesCustomCss());
        $this->assertFalse($settings->canDismiss());
        $this->assertNull($settings->getNotification('banner'));
        $this->assertNull($settings->getNotification('popup'));
    }

    public function testFlagsAreCastToBooleans(): void
    {
        $settings = (new Settings())->setActive(1)->setUseCustomCss('1')->setCanDismiss(null);

        $this->assertTrue($settings->isActive());
        $this->assertTrue($settings->usesCustomCss());
        $this->assertFalse($settings->canDismiss());
    }

    public function testNotificationsAreStoredPerStyle(): void
    {
        $banner = new Notification();
        $popup = new Notification();
        $settings = (new Settings())->setNotification('banner', $banner)->setNotification('popup', $popup);

        $this->assertSame($banner, $settings->getNotification('banner'));
        $this->assertSame($popup, $settings->getNotification('popup'));

        $settings->setNotification('banner', null);
        $this->assertNull($settings->getNotification('banner'));
        $this->assertSame($popup, $settings->getNotification('popup'));
    }

    public function testUnknownStyleIsIgnored(): void
    {
        $settings = (new Settings())->setNotification('toast', new Notification());

        $this->assertNull($settings->getNotification('toast'));
        $this->assertNull($settings->getNotification('banner'));
        $this->assertNull($settings->getNotification('popup'));
    }
}
