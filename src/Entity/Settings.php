<?php

namespace NotificationBannerKit\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use ClassKit\Entity\Core\UpdatedGuidEntity;

/**
 * Package settings. There is only ever one row.
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="nbSettings"
 * )
 * options={
 *      "comment": "Notification banner package settings"
 * }
 */
class Settings extends UpdatedGuidEntity
{
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false, "comment": "Master switch for showing notifications"})
     */
    protected $activate = false;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false, "comment": "Skip the bundled stylesheets"})
     */
    protected $useCustomCss = false;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false, "comment": "Whether banners can be dismissed"})
     */
    protected $canDismiss = false;

    /**
     * @ORM\ManyToOne(targetEntity="NotificationBannerKit\Entity\Notification")
     * @ORM\JoinColumn(name="bannerNotificationId", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $bannerNotification;

    /**
     * @ORM\ManyToOne(targetEntity="NotificationBannerKit\Entity\Notification")
     * @ORM\JoinColumn(name="popupNotificationId", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $popupNotification;

    /**
     * The saved settings, or an unsaved instance with the defaults (everything off) if none have been saved yet.
     * Never writes, so it is safe to call on every page render.
     */
    public static function get(): self
    {
        $settings = app(EntityManagerInterface::class)->getRepository(self::class)->findOneBy([]);

        return $settings instanceof self ? $settings : new self();
    }

    public function isActive(): bool
    {
        return (bool) $this->activate;
    }

    public function setActive($activate): self
    {
        $this->activate = (bool) $activate;

        return $this;
    }

    public function usesCustomCss(): bool
    {
        return (bool) $this->useCustomCss;
    }

    public function setUseCustomCss($useCustomCss): self
    {
        $this->useCustomCss = (bool) $useCustomCss;

        return $this;
    }

    public function canDismiss(): bool
    {
        return (bool) $this->canDismiss;
    }

    public function setCanDismiss($canDismiss): self
    {
        $this->canDismiss = (bool) $canDismiss;

        return $this;
    }

    /**
     * The notification chosen for a style ('banner' or 'popup'), if any.
     */
    public function getNotification(string $style): ?Notification
    {
        return match ($style) {
            'banner' => $this->bannerNotification,
            'popup' => $this->popupNotification,
            default => null,
        };
    }

    public function setNotification(string $style, ?Notification $notification): self
    {
        match ($style) {
            'banner' => $this->bannerNotification = $notification,
            'popup' => $this->popupNotification = $notification,
            default => null,
        };

        return $this;
    }
}
