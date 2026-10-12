<?php

declare(strict_types=1);

namespace NotificationBannerKit\Tests\Elements;

use PHPUnit\Framework\TestCase;

final class FrontEndElementsTest extends TestCase
{
    private function render(string $element, string $style): string
    {
        $payload = '"><script>alert(1)</script>';
        $page = new class($payload) {
            public function __construct(private string $p) {}
            public function getCollectionLink()
            {
                return $this->p;
            }
            public function getCollectionName()
            {
                return $this->p;
            }
        };
        $notification = new class($payload, $page) {
            public function __construct(private string $p, private object $page) {}
            public function getName()
            {
                return $this->p;
            }
            public function getContent()
            {
                return '<p>ok</p>';
            }
            public function getLinkText()
            {
                return $this->p;
            }
            public function getPage()
            {
                return $this->page;
            }
            public function getImage()
            {
                return false;
            }
        };
        $id = '1"><script>alert(1)</script>';
        $dismissable = true;

        ob_start();
        try {
            include __DIR__ . '/../../elements/dashboard/' . $element . '.php';

            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    public function testBannerEscapesAllValues(): void
    {
        $style = '"><script>alert(1)</script>';
        $html = $this->render('notification_banner', $style);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString('&lt;script', $html);
    }

    public function testPopupEscapesAllValues(): void
    {
        $style = '"><script>alert(1)</script>';
        $html = $this->render('notification_popup', $style);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString('&lt;script', $html);
    }
    public static function setUpBeforeClass(): void
    {
        defined('C5_EXECUTE') || define('C5_EXECUTE', true);
        defined('APP_CHARSET') || define('APP_CHARSET', 'UTF-8');
        // Core's helpers.php needs a booted app for t(), so define t() as a stub and h() exactly as core does.
        if (!function_exists('t')) {
            eval('function t($text, ...$args) { return $args ? vsprintf($text, $args) : $text; }');
        }
        if (!function_exists('h')) {
            eval('function h($input) { return (new \\Concrete\\Core\\Utility\\Service\\Text())->specialchars($input); }');
        }
        if (!class_exists('View', false)) {
            eval('class View { public static function element() {} }');
        }
    }
}
