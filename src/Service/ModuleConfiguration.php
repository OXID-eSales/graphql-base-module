<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\GraphQL\Base\Exception\MissingSignatureKey;

use function bin2hex;
use function random_bytes;
use function strlen;

/**
 * Class ModuleConfiguration
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 */
class ModuleConfiguration
{
    public const SIGNATUREKEYNAME = 'sJsonWebTokenSignature';

    public const LIFETIMENAME = 'sJsonWebTokenLifetime';

    public const QUOTANAME = 'sJsonWebTokenUserQuota';

    /** @var string[] */
    private $lifetimeMap = [
        '15min' => '+15 minutes',
        '1hrs' => '+1 hour',
        '3hrs' => '+3 hours',
        '8hrs' => '+8 hours',
        '24hrs' => '+24 hours',
    ];

    public function __construct(
        private ModuleSettingServiceInterface $moduleSettingService
    ) {
    }

    public function generateSignatureKey(): string
    {
        return bin2hex(random_bytes(64));
    }

    /**
     * @throws MissingSignatureKey
     */
    public function getSignatureKey(): string
    {
        $signature = $this->moduleSettingService
            ->getString(static::SIGNATUREKEYNAME, 'oe_graphql_base')
            ->trim()
            ->toString();

        if (strlen($signature) < 64) {
            throw MissingSignatureKey::wrongSize();
        }

        return $signature;
    }

    public function getTokenLifeTime(): string
    {
        $key = $this->moduleSettingService
            ->getString(static::LIFETIMENAME, 'oe_graphql_base')
            ->toString();

        return $this->lifetimeMap[$key] ?? $this->lifetimeMap['8hrs'];
    }

    public function getUserTokenQuota(): int
    {
        return $this->moduleSettingService->getInteger(static::QUOTANAME, 'oe_graphql_base');
    }
}
