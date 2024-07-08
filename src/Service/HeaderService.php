<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

class HeaderService implements HeaderServiceInterface
{
    public function cleanCurrentHeaders(): void
    {
        $headers = $this->getHeaders();

        $toRestore = $this->collectWhitelistedHeaders($headers);

        header_remove();

        $this->restoreHeaders($toRestore);
    }

    /**
     * @return String[]
     */
    protected function getHeaders(): array
    {
        return headers_list();
    }

    /**
     * @param String[] $headers
     * @return String[]
     */
    protected function collectWhitelistedHeaders(array $headers): array
    {
        $whitelisted = [];

        foreach ($headers as $header) {
            if ($this->shouldKeepHeader($header)) {
                $whitelisted[] = $header;
            }
        }

        return $whitelisted;
    }

    protected function shouldKeepHeader(string $header): bool
    {
        $cookiePattern = sprintf('Set-Cookie: %s', FingerprintService::COOKIE_KEY);

        return stripos($header, $cookiePattern) !== false;
    }

    /**
     * @param String[] $toRestore
     */
    protected function restoreHeaders(array $toRestore): void
    {
        foreach ($toRestore as $header) {
            header($header);
        }
    }
}
