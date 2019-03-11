<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use OxidEsales\GraphQl\Type\BaseType;
use OxidEsales\GraphQl\Type\Provider\MutationProviderInterface;
use OxidEsales\GraphQl\Type\Provider\QueryProviderInterface;

/**
 * Class SchemaFactory
 *
 * @package OxidProfessionalServices\GraphQl\Core\Schema
 */
class SchemaFactory implements SchemaFactoryInterface
{

    private $schema = null;

    private $queryProviders = [];

    private $mutationProviders = [];

    /**
     * @param BaseType $type
     */
    public function addQueryProvider(QueryProviderInterface $provider)
    {
        $this->queryProviders[] = $provider;
    }

    /**
     * @param BaseType $type
     */
    public function addMutationProvider(MutationProviderInterface $provider)
    {
        $this->mutationProviders[] = $provider;
    }

    /**
     * @return Schema
     */
    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $executableSchema = [
            'query'    => $this->createQueryType(),
            'mutation' => $this->createMutationType()
        ];

        $this->schema = new Schema($executableSchema);

        return $this->schema;
    }

    private function createQueryType()
    {
        $fields = [];
        $fieldHandlers = [];

        foreach ($this->queryProviders as $provider) {
            foreach ($provider->getQueries() as $fieldName => $field) {
                $fields[$fieldName] = $field;
            }
            foreach ($provider->getQueryResolvers() as $handlerName => $handler) {
                $fieldHandlers[$handlerName] = $handler;
            }
        }

        $config = [
            'name' => 'query',
            'description' => 'The base query type',
            'fields' => $fields,
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) use ($fieldHandlers) {
                return $fieldHandlers[$info->fieldName]($val, $args, $context, $info);
            },
        ];

        return new ObjectType($config);
    }

    private function createMutationType()
    {
        $fields = [];
        $fieldHandlers = [];

        foreach ($this->mutationProviders as $provider) {
            foreach ($provider->getMutations() as $fieldName => $field) {
                $fields[$fieldName] = $field;
            }
            foreach ($provider->getMutationResolvers() as $handlerName => $handler) {
                $fieldHandlers[$handlerName] = $handler;
            }
        }

        $config = [
            'name' => 'mutation',
            'description' => 'The base mutation type',
            'fields' => $fields,
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) use ($fieldHandlers) {
                return $fieldHandlers[$info->fieldName]($val, $args, $context, $info);
            },
        ];

        return new ObjectType($config);

    }
}
