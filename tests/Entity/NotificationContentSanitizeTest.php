<?php

declare(strict_types=1);

namespace NotificationBannerKit\Tests\Entity;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use NotificationBannerKit\Entity\NotificationContent;

final class NotificationContentSanitizeTest extends TestCase
{
    public static function payloads(): array
    {
        return [
            'script tag' => ['<p>a</p><script>alert(1)</script>', '<script'],
            'onclick' => ['<p onclick="x()">a</p>', 'onclick'],
            'img onerror' => ['<img src=x onerror=alert(1)>', 'onerror'],
            'javascript href' => ['<a href="javascript:alert(1)">a</a>', 'href="javascript:'],
            'entity-obfuscated javascript href' => ['<a href="jav&#x61;script:alert(1)">a</a>', 'href="javascript:'],
            'data href' => ['<a href="data:text/html,<script>alert(1)</script>">a</a>', 'href="data:'],
            'iframe' => ['<iframe src="//evil"></iframe>', '<iframe'],
            'svg onload' => ['<svg onload=alert(1)></svg>', 'onload'],
            'form' => ['<form action="//evil"><input></form>', '<form'],
        ];
    }

    #[DataProvider('payloads')]
    public function testDangerousMarkupIsRemoved(string $input, string $forbidden): void
    {
        $method = new \ReflectionMethod(NotificationContent::class, 'sanitizeHtml');
        $out = $method->invoke((new \ReflectionClass(NotificationContent::class))->newInstanceWithoutConstructor(), $input);

        $this->assertStringNotContainsStringIgnoringCase($forbidden, $out);
    }

    public function testSafeMarkupIsKept(): void
    {
        $method = new \ReflectionMethod(NotificationContent::class, 'sanitizeHtml');
        $out = $method->invoke((new \ReflectionClass(NotificationContent::class))->newInstanceWithoutConstructor(), '<p>Hi <strong>there</strong> <a href="https://example.com/x">link</a></p>');

        $this->assertSame('<p>Hi <strong>there</strong> <a href="https://example.com/x">link</a></p>', $out);
    }
}
