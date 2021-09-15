<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework\Service;

use function bin2hex;
use function is_string;
use function random_bytes;
use function strlen;

/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 */
class KeyRegistry extends \OxidEsales\GraphQL\Base\Service\KeyRegistry
{

    /**
     * @throws MissingSignatureKey
     */
    public function getSignatureKey(): string
    {
        return '5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==';
    }
}
