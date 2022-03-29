<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Middleware;

use GraphQL\Type\Definition\FieldDefinition;
use TheCodingMachine\GraphQLite\Middlewares\FieldHandlerInterface;
use TheCodingMachine\GraphQLite\Middlewares\FieldMiddlewareInterface;
use TheCodingMachine\GraphQLite\QueryFieldDescriptor;

class HideFieldMiddleware implements FieldMiddlewareInterface
{
    public function process(QueryFieldDescriptor $queryFieldDescriptor, FieldHandlerInterface $fieldHandler): ?FieldDefinition
    {
        $annotationName = $queryFieldDescriptor->getName();

        $hideQueriesMutations = [
            'products',
            'vendor'
        ];

        if (
            in_array(
                strtolower($annotationName),
                array_map('strtolower', $hideQueriesMutations)
            )
        ) {
            return null;
        }

        return $fieldHandler->handle($queryFieldDescriptor);
    }
}
