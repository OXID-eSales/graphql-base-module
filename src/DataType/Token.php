<?php

namespace OxidEsales\GraphQL\Base\DataType;

use GraphQL\Type\Definition\ResolveInfo;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
class Token
{
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @Field()
     */
    public function token(ResolveInfo $resolveInfo): string
    {
        return $this->token;
    }
}
