<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\Controller;

use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Logged;

final class controller
{
    /**
     * @Query()
     * @Logged()
     */
    public function basket(): Basket {
        // ...
    }
}
