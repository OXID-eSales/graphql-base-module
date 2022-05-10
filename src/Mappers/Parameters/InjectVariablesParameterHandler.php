<?php

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Mappers\Parameters;

use OxidEsales\GraphQL\Base\Annotation\InjectVariables;
use OxidEsales\GraphQL\Base\Parameters\InjectVariablesParameter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TheCodingMachine\GraphQLite\Parameters\ParameterInterface;
use TheCodingMachine\GraphQLite\Mappers\Parameters\ParameterMiddlewareInterface;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotations;
use TheCodingMachine\GraphQLite\Mappers\Parameters\ParameterHandlerInterface;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Type;
use ReflectionParameter;

class InjectVariablesParameterHandler implements ParameterMiddlewareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function mapParameter(ReflectionParameter $parameter, DocBlock $docBlock, ?Type $paramTagType, ParameterAnnotations $parameterAnnotations, ParameterHandlerInterface $next): ParameterInterface
    {
        // The $parameterAnnotations object can be used to fetch any annotation implementing ParameterAnnotationInterface
        $autowire = $parameterAnnotations->getAnnotationByType(InjectVariables::class);

        if ($autowire === null) {
            // If there are no annotation, this middleware cannot handle the parameter. Let's ask
            // the next middleware in the chain (using the $next object)
            return $next->mapParameter($parameter, $docBlock, $paramTagType, $parameterAnnotations);
        }

        // We found a @Autowire annotation, let's return a parameter resolver.
        return new InjectVariablesParameter($this->container, $parameter->getName());
    }
}
