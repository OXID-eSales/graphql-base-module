<?php

namespace OxidEsales\GraphQL\Base\Middlewares;

use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Annotations\Security;
use TheCodingMachine\GraphQLite\Middlewares\FieldHandlerInterface;
use TheCodingMachine\GraphQLite\Middlewares\FieldMiddlewareInterface;
use GraphQL\Type\Definition\FieldDefinition;
use TheCodingMachine\GraphQLite\QueryFieldDescriptor;

class CommentFieldMiddleware implements FieldMiddlewareInterface
{
    public function process(QueryFieldDescriptor $queryFieldDescriptor, FieldHandlerInterface $fieldHandler): ?FieldDefinition
    {
        $annotations = $queryFieldDescriptor->getMiddlewareAnnotations();

        $loggedAnnotation   = $annotations->getAnnotationByType(Logged::class);
        $rightAnnotation    = $annotations->getAnnotationByType(Right::class);
        $securityAnnotation = $annotations->getAnnotationByType(Security::class);

        if ($loggedAnnotation instanceof Logged || $rightAnnotation instanceof Right || $securityAnnotation instanceof Security) {
            $comment = "Authorisation is required!<br>" . $queryFieldDescriptor->getComment();
            $queryFieldDescriptor->setComment($comment);
        }

        return $fieldHandler->handle($queryFieldDescriptor);
    }
}
