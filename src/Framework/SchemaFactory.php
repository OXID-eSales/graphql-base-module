<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use Mouf\Composer\ClassNameMapper;
use TheCodingMachine\GraphQLite\Schema;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use TheCodingMachine\GraphQLite\SchemaFactory as GraphQLiteSchemaFactory;

/**
 * Class SchemaFactory
 *
 * @package OxidProfessionalServices\GraphQl\Core\Schema
 */
class SchemaFactory implements SchemaFactoryInterface
{
    /** @var Schema */
    private $schema = null;

    private $namespaceMappers = null;

    public function __construct(iterable $namespaceMappers)
    {
        $this->namespaceMappers = $namespaceMappers;
    }

    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $factory = new GraphQLiteSchemaFactory(
            new \Symfony\Component\Cache\Simple\NullCache(),
            ContainerFactory::getInstance()->getContainer()
        );

        $classNameMapper = new ClassNameMapper();

        foreach ($this->namespaceMappers as $namespaceMapper) {
            /** @var $namespaceMapper NamespaceMapperInterface */
            foreach ($namespaceMapper->getControllerNamespaceMapping() as $namespace=>$path) {
                $classNameMapper->registerPsr4Namespace(
                    $namespace,
                    $path
                );
                $factory->addControllerNameSpace($namespace);
            }
            /** @var $namespaceMapper NamespaceMapperInterface */
            foreach ($namespaceMapper->getTypeNamespaceMapping() as $namespace=>$path) {
                $classNameMapper->registerPsr4Namespace(
                    $namespace,
                    $path
                );
                $factory->addTypeNameSpace($namespace);
            }
        }

        $factory->setClassNameMapper($classNameMapper);

        return $this->schema = $factory->createSchema();
    }
}
