<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event\Subscriber;

use OxidEsales\GraphQL\Base\Event\BeforeTokenCreation;
use OxidEsales\GraphQL\Base\Service\FingerprintServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BeforeTokenCreationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FingerprintServiceInterface $fingerprintService
    ) {
    }

    public function handle(BeforeTokenCreation $event): BeforeTokenCreation
    {
        $builder = $event->getBuilder();
        $fingerprint = $this->fingerprintService->getFingerprint();

        $builder->withClaim(
            name: FingerprintServiceInterface::TOKEN_KEY,
            value: $this->fingerprintService->hashFingerprint($fingerprint)
        );

        return $event;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeTokenCreation::class => 'handle',
        ];
    }
}
