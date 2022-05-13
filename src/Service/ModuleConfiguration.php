<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\GraphQL\Base\Exception\MissingSignatureKey;

use function bin2hex;
use function is_string;
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

    /** @var ModuleSettingBridgeInterface */
    private $moduleSettingBridge;

    /** @var string[] */
    private $lifetimeMap = [
        '15min' => '+15 minutes',
        '1hrs' => '+1 hour',
        '3hrs' => '+3 hours',
        '8hrs' => '+8 hours',
        '24hrs' => '+24 hours',
    ];

    public function __construct(
        ModuleSettingBridgeInterface $moduleSettingBridge
    ) {
        $this->moduleSettingBridge = $moduleSettingBridge;
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
        $signature = $this->moduleSettingBridge->get(static::SIGNATUREKEYNAME, 'oe_graphql_base');

        if (!is_string($signature)) {
            throw MissingSignatureKey::wrongType();
        }

        if (strlen($signature) < 64) {
            throw MissingSignatureKey::wrongSize();
        }

        return $signature;
    }

    public function getTokenLifeTime(): string
    {
        $key = (string)$this->moduleSettingBridge->get(static::LIFETIMENAME, 'oe_graphql_base');

        return $this->lifetimeMap[$key] ?? $this->lifetimeMap['8hrs'];
    }

    public function getUserTokenQuota(): int
    {
        return (int)$this->moduleSettingBridge->get(static::QUOTANAME, 'oe_graphql_base');
    }
}
