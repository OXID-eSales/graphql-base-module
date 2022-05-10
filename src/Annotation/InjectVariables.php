<?php

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Annotation;

use Attribute;
use BadMethodCallException;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotationInterface;

use function ltrim;

/**
 * Use this annotation to tell GraphQLite to inject the current logged user as an input parameter.
 * If the parameter is not nullable, the user MUST be logged to access the resource.
 *
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 * @Attributes({
 *   @Attribute("for", type = "string")
 * })
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class InjectVariables implements ParameterAnnotationInterface
{
    /** @var string */
    private $for;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = [])
    {
        if (! isset($values['for'])) {
            return;
        }

        $this->for = ltrim($values['for'], '$');
    }

    public function getTarget(): string
    {
        if ($this->for === null) {
            throw new BadMethodCallException('The @InjectVariables annotation must be passed a target. For instance: "@InjectVariables(for="$variables")"');
        }

        return $this->for;
    }
}
