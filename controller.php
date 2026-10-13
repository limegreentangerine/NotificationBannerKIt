<?php

namespace Concrete\Package\NotificationBannerKit;

use Concrete\Core\Entity\Package;
use ClassKit\Package\PackageController;
use Concrete\Core\Support\Facade\Events;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBannerKit\Entity\{Notification, Settings};
use ClassKit\Package\Traits\{AttributeTrait, BlockTrait, PageTrait};

class Controller extends PackageController
{
    use AttributeTrait;
    use BlockTrait;
    use PageTrait;

    /**
     * The packages handle.
     * Note that this must be unique in the
     * entire concrete5 package ecosystem.
     *
     * @var string
     */
    protected $pkgHandle = 'notification_banner_kit';

    /**
     * The packages version.
     *
     * @var string
     */
    protected $pkgVersion = '0.0.3';

    /**
     * The minimum Concrete version compatible with the package.
     * Override this value according to the minimum required version for your package.
     *
     * @var string
     */
    protected $appVersionRequired = '9.5.0';

    /**
     * The minimum PHP version compatible with the package.
     * Override this value according to the minimum required version for your package.
     *
     * @var string
     * @var string
     */
    protected $phpVersionRequired = '8.4';

    /**
     * Package service providers to register.
     *
     * eg. 'Concrete\Package\PackageHandle\Src\Providers\PackageServiceProvider'
     *
     * @var array
     */
    protected $providers = [];

    /**
     * An array describing the package dependencies.
     * Keys are package handles.
     * Values may be:
     * - false: this package can't be installed if the other package is already installed.
     * - true: this package can't be installed of the other package is not installed
     * - a string: this package can't be installed of the other package is not installed or it's installed with an older version
     * - an array with two strings, representing the minimum and the maximum version of the other package to be installed.
     *
     * @var array
     *
     * @example [
     *     // This package can't be installed if a package with handle other_package_1 is already installed.
     *     'other_package_1' => false,
     *     // This package can't be installed if a package with handle other_package_2 is not installed.
     *     'other_package_2' => true,
     *     // This package can't be installed if a package with handle other_package_3 is not installed, or it has a version prior to 1.0
     *     'other_package_3' => '1.0',
     *     // This package can't be installed if a package with handle other_package_4 is not installed, or it has a version prior to 2.0, or it has a version after 2.9
     *     'other_package_4' => ['2.0', '2.9'],
     * ]
     */
    protected $packageDependencies = [
        'class_kit' => true,
    ];

    /**
     * Package class autoloader registrations
     * The package install helper class, included with this boilerplate,
     * is activated by default.
     *
     * @see https://goo.gl/4wyRtH
     * @var array
     */
    protected $pkgAutoloaderRegistries = [
        'src' => '\NotificationBannerKit',
    ];

    protected $tables = [
        'nbNotifications',
        'nbNotificationContents',
        'nbNotification_NotificationContents',
        'nbSettings',
    ];

    /**
     * Package tasks to register.
     *
     * eg. 'task_handle' => \PackageHandle\Command\Task\Controller\TaskHandleController::class,
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * Settings used to live in the package file config. Copy them into the nbSettings table the first time this runs.
     * The file config is left in place, so nothing is lost if the upgrade needs to be rolled back.
     */
    protected function migrateFileConfigToSettings(): void
    {
        $em = $this->app->make(EntityManagerInterface::class);

        if ($em->getRepository(Settings::class)->findOneBy([])) {
            return;
        }

        $config = $this->getFileConfig();
        $saved = array_filter(
            array_map(fn($key) => $config->get('notification_banner.' . $key), ['activate', 'use_custom_css', 'can_dismiss', 'notification_banner', 'notification_popup']),
            fn($value) => $value !== null,
        );

        if ($saved === []) {
            return;
        }

        $settings = (new Settings())
            ->setActive($config->get('notification_banner.activate'))
            ->setUseCustomCss($config->get('notification_banner.use_custom_css'))
            ->setCanDismiss($config->get('notification_banner.can_dismiss'));

        foreach (['banner', 'popup'] as $style) {
            $id = (string) $config->get('notification_banner.notification_' . $style);
            $notification = $id !== '' ? Notification::getByID($id) : false;

            if ($notification && $notification->getStyle() === $style) {
                $settings->setNotification($style, $notification);
            }
        }

        $em->persist($settings);
        $em->flush();
    }

    public function getPackageName()
    {
        return t('Notification Banner');
    }

    public function getPackageDescription()
    {
        return t('Notification Banner');
    }

    public function on_start()
    {
        parent::on_start();

        Events::addListener('on_before_render', function () {
            \NotificationBannerKit\Events\NotificationBanner::onBeforeRender();
        });
    }

    public function installOrUpgrade(Package $pkg)
    {
        // Add Dashboard Pages
        $this->addSinglePage('/dashboard/notification_banner', $pkg, t('Notification Banner'), t('Notification Banner Dashboard'));
        $this->addSinglePage('/dashboard/notification_banner/notifications', $pkg, t('Notifications'), t('Notification Banner Notifications'));
        $this->addSinglePage('/dashboard/notification_banner/settings', $pkg, t('Settings'), t('Notification Banner Settings'));

        $this->migrateFileConfigToSettings();
    }

    public function registerRoutes(): void {}

    public function registerEvents(): void {}
}
