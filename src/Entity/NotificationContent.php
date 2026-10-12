<?php

namespace NotificationBannerKit\Entity;

use Concrete\Core\Page\Page;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Editor\LinkAbstractor;
use ClassKit\Entity\Core\UpdatedGuidEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="nbNotificationContents",
 *      indexes={
 *          @ORM\Index(name="idx_fid", columns={"fID"}),
 *          @ORM\Index(name="idx_language", columns={"language"})
 *      }
 * )
 * options={
 *      "comment": "List of notification contents"
 * }
 */
class NotificationContent extends UpdatedGuidEntity
{
    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=false, options={"comment": "Notification language"})
     */
    protected $language;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, options={"comment": "Notification name"})
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\File\File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID", onDelete="SET NULL", nullable=true)
     */
    protected $fID = null;

    /**
     * @ORM\Column(type="text", nullable=false, options={"comment": "Notification content"})
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=255, options={"comment": "Notification link text"})
     */
    protected $linkText;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true, options={"comment": "Notification link cID"})
     */
    protected $linkCID = null;

    /**
     * Strip scripts, event handlers and unsafe URL schemes from rich text before it is output on the front end.
     */
    protected function sanitizeHtml(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return htmLawed($html, [
            'safe' => 1,
            'elements' => '*-script-iframe-object-embed-applet-form',
            'deny_attribute' => 'on*',
            'schemes' => 'href: http, https, mailto, tel; src: http, https',
        ]);
    }

    /**
     * Get the value of language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the value of language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of content
     */
    public function getContent(bool $raw = true, bool $editMode = false)
    {
        if ($raw) {
            return $this->content;
        }

        if ($editMode) {
            return LinkAbstractor::translateFromEditMode($this->content);
        }

        return $this->sanitizeHtml(LinkAbstractor::translateFrom($this->content));
    }

    /**
     * Set the value of content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of linkText
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * Set the value of linkText
     *
     * @return self
     */
    public function setLinkText($linkText)
    {
        $this->linkText = $linkText;

        return $this;
    }

    /**
     * Get the value of linkCID
     */
    public function getLinkCID()
    {
        return $this->linkCID;
    }

    /**
     * Set the value of linkCID
     *
     * @return self
     */
    public function setLinkCID($linkCID)
    {
        $this->linkCID = $linkCID;

        return $this;
    }

    /**
     * Get Link
     *
     * @return Page|false
     */
    public function getPage()
    {
        if ($this->linkCID === null || $this->linkCID <= 0) {
            return false;
        }

        $page = Page::getByID($this->linkCID);
        return !$page->isError() ? $page : false;
    }

    /**
     * Get the value of fID
     */
    public function getImage()
    {
        if ($this->fID === null || $this->fID <= 0) {
            return false;
        }
        $file = \Concrete\Core\File\File::getByID($this->fID);
        return ($file instanceof File && !$file->isError()) ? $file : false;
    }

    /**
     * Set the value of fID
     *
     * @return self
     */
    public function setImage($fID)
    {
        $this->fID = $fID;

        return $this;
    }
}
