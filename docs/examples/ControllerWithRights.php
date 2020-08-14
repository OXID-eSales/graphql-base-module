<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\Controller;

use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class controller
{
    /**
     * @Query()
     * @Logged()
     * @Right('SEE_BASKET')
     */
    public function basket(): Basket {
        // ...
    }
}
