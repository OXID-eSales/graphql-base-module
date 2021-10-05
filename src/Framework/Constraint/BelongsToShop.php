<?php
declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework\Constraint;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;
use OxidEsales\GraphQL\Base\Service\Authentication;

final class BelongsToShop implements Constraint
{
    /** @var int */
    private int $shopId;

    public function __construct(int $shopId)
    {
        $this->shopId = $shopId;
    }

    public function assert(Token $token): void
    {
        if (!$token instanceof UnencryptedToken) {
            throw new ConstraintViolation('You should pass a plain token');
        }

        if (!$token->claims()->has(Authentication::CLAIM_SHOPID)
            || $token->claims()->get(Authentication::CLAIM_SHOPID) !== $this->shopId) {
            throw new ConstraintViolation(
                'The token shop id doesnt match'
            );
        }
    }
}
