<?php

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Parameters;

use GraphQL\Type\Definition\ResolveInfo;
use TheCodingMachine\GraphQLite\Parameters\ParameterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A parameter filled from the current user.
 */
class InjectVariablesParameter implements ParameterInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $identifier;

    public function __construct(ContainerInterface $container, string $identifier)
    {
        $this->container = $container;
        $this->identifier = $identifier;
    }

    /**
     * @param array<string, mixed> $args
     * @param mixed                $context
     *
     * @return mixed
     */
    public function resolve(?object $source, array $args, $context, ResolveInfo $info)
    {
//        var_dump($this->identifier);
//        var_dump($args); //contain all input properties
//        var_dump($context);exit;
        return $args;
    }
}
