<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;

/**
 * Class SchemaFactory
 *
 * @package OxidProfessionalServices\GraphQl\Core\Schema
 */
class SchemaFactory implements SchemaFactoryInterface
{

    private $schema = null;

    private $queryType;

    private $mutationType;

    /**
     * SchemaFactory constructor.
     *
     * @param TypeFactory $queryTypeFactory
     * @param TypeFactory $mutationTypeFactory
     */
    public function __construct(TypeFactory $queryTypeFactory, TypeFactory $mutationTypeFactory)
    {
        $this->queryType = $queryTypeFactory->getType();
        $this->mutationType = $mutationTypeFactory->getType();
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
            'query'    => $this->queryType,
            'mutation' => $this->mutationType
        ];

        $this->schema = new Schema($executableSchema);

        return $this->schema;
    }
}
