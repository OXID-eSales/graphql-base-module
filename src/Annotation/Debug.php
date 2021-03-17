<?php

namespace OxidEsales\GraphQL\Base\Annotation;

use TheCodingMachine\GraphQLite\Annotations\MiddlewareAnnotationInterface;
use Attribute;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 * @Attributes({
 *   @Attribute("debug", type = "string"),
 * })
 */
class Debug implements MiddlewareAnnotationInterface
{
    /** @var string */
    private $debug;

    public function __construct($data = [])
    {
        $this->debug = $data['value'] ?? '-';
    }

    public function getDebug(): string
    {
        return $this->debug;
    }
}
