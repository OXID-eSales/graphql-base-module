<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Utility;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker;
use OxidEsales\Eshop\Core\PasswordSaltGenerator;
use OxidEsales\Eshop\Core\Registry;
use Psr\Log\LoggerInterface;

class LegacyWrapper implements LegacyWrapperInterface
{
    /** @var LoggerInterface  */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createUid(): string
    {
        return Registry::getUtilsObject()->generateUId();
    }

    public function setLanguage(string $languageShortcut): void
    {
        $language = Registry::getLang();
        $availableLanguages = $language->getLanguageIds(Registry::getConfig()->getShopId());

        $index = array_search($languageShortcut, $availableLanguages);
        if ($index !== false) {
            $language->setBaseLanguage($index);
        }

    }

}
