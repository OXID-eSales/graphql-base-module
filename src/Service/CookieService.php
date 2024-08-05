<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;

class CookieService implements CookieServiceInterface
{
    public const LIFETIME_SECONDS = 31500000; // about 1 year

    private const SETTING_MAP = [
        ModuleConfiguration::COOKIE_SETTING_SAME => [
            'samesite' => 'Lax',
        ],
        ModuleConfiguration::COOKIE_SETTING_CROSS => [
            'samesite' => 'None',
            'secure' => true,
        ],
    ];

    public function __construct(
        private readonly ModuleConfiguration $moduleConfiguration
    ) {
    }

    public function setFingerprintCookie(string $fingerprint): void
    {
        setcookie(
            name: FingerprintServiceInterface::COOKIE_KEY,
            value: $fingerprint,
            expires_or_options: $this->getFingerprintOptions()
        );
    }

    public function getFingerprintCookie(): string
    {
        if (!key_exists(FingerprintServiceInterface::COOKIE_KEY, $_COOKIE)) {
            throw new FingerprintMissingException("Fingerprint missing");
        }

        return $_COOKIE[FingerprintServiceInterface::COOKIE_KEY];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFingerprintOptions(): array
    {
        $defaults = [
            'httponly' => true,
            'expires' => time() + self::LIFETIME_SECONDS,
        ];

        $setting = $this->moduleConfiguration->getCookieSetting();

        $options = array_merge($defaults, self::SETTING_MAP[$setting]);

        return $options;
    }
}
