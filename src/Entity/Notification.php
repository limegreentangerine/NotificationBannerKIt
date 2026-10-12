<?php

namespace NotificationBannerKit\Entity;

use Concrete\Core\Page\Page;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use ClassKit\Entity\Core\UpdatedGuidEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="nbNotifications"
 * )
 * options={
 *      "comment": "List of notifications available for display"
 * }
 */
class Notification extends UpdatedGuidEntity
{
    public const DEFAULT_LANGUAGE = 'en';

    /**
     * Many Notifications have Many NotificationContents.
     * @ORM\ManyToMany(targetEntity="NotificationBannerKit\Entity\NotificationContent")
     * @ORM\JoinTable(name="nbNotification_NotificationContents",
     *      joinColumns={
     *          @ORM\JoinColumn(name="notificationId", referencedColumnName="id", onDelete="cascade")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="notificationContentId", referencedColumnName="id", onDelete="cascade")
     *      }
     * )
     */
    protected $data;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=false, options={"comment": "Notification style handle"})
     */
    protected $style;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true, "comment": "Whether this notification is available for selection"})
     */
    protected $active = true;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment": "Notification will not display before this date/time"})
     */
    protected $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment": "Notification will not display after this date/time"})
     */
    protected $endDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, options={"default": "all", "comment": "Which pages this notification displays on: all or home"})
     */
    protected $displayScope = 'all';

    public function __construct()
    {
        parent::__construct();
        $this->data = new ArrayCollection();
    }

    /**
     * Get many Notifications have Many NotificationContents.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get Contents
     */
    public function getBanner(string $lang = self::DEFAULT_LANGUAGE)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('language', $lang))
            ->setMaxResults(1);

        $matching = $this->data->matching($criteria);
        return $matching->isEmpty() ? false : $matching->first();
    }

    /**
     * Add Data to Notification
     */
    public function addData($data)
    {
        if (!$this->data->contains($data)) {
            $this->data->add($data);
        }
        return $this;
    }

    /**
     * Remove Data from Notification
     */
    public function removeData($data)
    {
        if ($this->data->contains($data)) {
            $this->data->remove($data);
        }

        return $this;
    }

    public function getName(string $lang = self::DEFAULT_LANGUAGE)
    {
        $banner = $this->getBanner($lang);

        return ($banner) ? $banner->getName() : '';
    }

    public function getContent(string $lang = self::DEFAULT_LANGUAGE)
    {
        $banner = $this->getBanner($lang);

        return ($banner) ? app('helper/text')->sanitize($banner->getContent()) : '';
    }

    /**
     * Get the value of style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get the value of style
     */
    public function getStyleString()
    {
        return ucfirst($this->style);
    }

    /**
     * Set the value of style
     *
     * @return self
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Get the value of active
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * Set the value of active
     *
     * @return self
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;

        return $this;
    }

    /**
     * Get the value of startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Whether the current date/time falls within this notification's scheduled window (if any)
     */
    public function isWithinSchedule(?\DateTimeInterface $now = null)
    {
        $now = $now ?? new \DateTime();

        if ($this->startDate !== null && $now < $this->startDate) {
            return false;
        }

        if ($this->endDate !== null && $now > $this->endDate) {
            return false;
        }

        return true;
    }

    /**
     * Get the value of displayScope
     */
    public function getDisplayScope()
    {
        return $this->displayScope;
    }

    /**
     * Set the value of displayScope
     *
     * @return self
     */
    public function setDisplayScope($displayScope)
    {
        $this->displayScope = $displayScope;

        return $this;
    }

    /**
     * Whether this notification should display on the given page, based on its display scope
     */
    public function isVisibleOnPage(Page $page)
    {
        if ($this->displayScope === 'home') {
            return $page->isHomePage();
        }

        return true;
    }
}
