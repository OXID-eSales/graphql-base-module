<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Error;

use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class AuthorizationError extends AbstractError
{
    public const UNAUTHORIZED_DELETE_TOKEN = 'oegqlb.authorized.delete_token';
    public const UNAUTHORIZED_VIEW_TOKEN = 'oegqlb.authorized.view_token';

    public function __construct(string $code)
    {
        parent::__construct($code, $this->messages()[$code]);
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            self::UNAUTHORIZED_DELETE_TOKEN => 'You are not authorized to delete this token.',
            self::UNAUTHORIZED_VIEW_TOKEN => 'You are not authorized to view this token.',
        ];
    }
}
