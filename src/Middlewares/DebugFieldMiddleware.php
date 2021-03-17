<?php

namespace OxidEsales\GraphQL\Base\Middlewares;

use OxidEsales\GraphQL\Base\Annotation\Debug;
use TheCodingMachine\GraphQLite\Middlewares\FieldHandlerInterface;
use TheCodingMachine\GraphQLite\Middlewares\FieldMiddlewareInterface;
use GraphQL\Type\Definition\FieldDefinition;
use TheCodingMachine\GraphQLite\QueryFieldDescriptor;
use OxidEsales\GraphQL\Base\Service\Authorization;

class DebugFieldMiddleware implements FieldMiddlewareInterface
{
    private $authorization;

    public function __construct(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    public function process(QueryFieldDescriptor $queryFieldDescriptor, FieldHandlerInterface $fieldHandler): ?FieldDefinition
    {
        $annotations = $queryFieldDescriptor->getMiddlewareAnnotations();

        /**
         * @var Debug $debug
         */
        $debug = $annotations->getAnnotationByType(Debug::class);

        //Annotation does not present, skip
        if ($debug !== null) {
            if (!$this->authorization->isAllowed($debug->getDebug())) {
                //Replace field comment
                $queryFieldDescriptor->setComment('This field may not accessible by you');

                //Change field name
                $queryFieldDescriptor->setName(
                    $queryFieldDescriptor->getName() . '_private'
                );

                //Remove field parameters
                $queryFieldDescriptor->setParameters([]);

                //return null to hide field from schema
//                return null;
            }
        }

        // Otherwise, let's continue the middleware pipe without touching anything.
        return $fieldHandler->handle($queryFieldDescriptor);
    }
}
