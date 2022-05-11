<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use OxidEsales\GraphQL\Base\Framework\Constraint\BelongsToShop;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;

class JwtConfigurationBuilder
{
    /** @var ModuleConfiguration */
    private $moduleConfiguration;

    /** @var LegacyService */
    private $legacyService;

    public function __construct(
        ModuleConfiguration $moduleConfiguration,
        LegacyService $legacyService
    ) {
        $this->moduleConfiguration = $moduleConfiguration;
        $this->legacyService       = $legacyService;
    }

    public function getConfiguration(): Configuration
    {
        $config = Configuration::forSymmetricSigner(
            new Sha512(),
            InMemory::plainText($this->moduleConfiguration->getSignatureKey())
        );

        $strictValidAt = new StrictValidAt(SystemClock::fromSystemTimezone());
        $issuedBy      = new IssuedBy($this->legacyService->getShopUrl());
        $permittedFor  = new PermittedFor($this->legacyService->getShopUrl());
        $signedWith    = new SignedWith($config->signer(), $config->verificationKey());
        $belongsToShop = new BelongsToShop($this->legacyService->getShopId());

        $config->setValidationConstraints($issuedBy, $permittedFor, $signedWith, $belongsToShop, $strictValidAt);

        return $config;
    }
}
