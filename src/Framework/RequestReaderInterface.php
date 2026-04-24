<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\UnableToParseToken;

interface RequestReaderInterface
{
    /**
     * Returns the authenticated token parsed from the incoming request.
     *
     * @throws UnableToParseToken When the Authorization header carries a malformed JWT.
     * @throws InvalidToken       When the JWT fails validation (signature, claims, expiry, registry).
     */
    public function getAuthToken(): ?UnencryptedToken;

    /**
     * Returns the GraphQL query data decoded from the incoming request.
     *
     * Note: the declared type reflects the existing concrete implementation's
     * contract — in practice each value can be `null` when absent from the
     * incoming payload, which callers must handle. Tightening this signature
     * to `string|null` values is deferred until a follow-up that also hardens
     * `GraphQLQueryHandler::executeQuery()` to reject null queries cleanly.
     *
     * @return array{query: string, variables: string[], operationName: string}
     */
    public function getGraphQLRequestData(string $inputFile = 'php://input'): array;
}
