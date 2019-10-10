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
    private $schema = null;

    /**
     * @return Schema
     */
    public function getSchema(): Schema
    {
        if (null !== $this->schema) {
            return $this->schema;
        }

        $classNameMapper = new ClassNameMapper();
        $classNameMapper->registerPsr4Namespace(
            '\\OxidEsales\\GraphQl',
            __DIR__.'/../'
        );

        $factory = new GraphQLiteSchemaFactory(
            new \Symfony\Component\Cache\Simple\NullCache(),
            ContainerFactory::getInstance()->getContainer()
        );
        $factory->addControllerNamespace('\\OxidEsales\\GraphQl\\Controllers')
                ->addTypeNamespace('\\OxidEsales\\GraphQl\\DataObject')
                ->setClassNameMapper($classNameMapper);

        // TODO: call all modules and give them the factory,
        // so they can register their controller and type
        // namespaces

        return $this->schema = $factory->createSchema();
    }
}
