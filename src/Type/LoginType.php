<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

/**
 * Class LoginType
 *
 * @package OxidEsales\GraphQl\Type
 */
class LoginType extends BaseType
{

    /** @var AuthenticationServiceInterface $authService */
    private $authService;

    /** @var  KeyRegistryInterface $keyRegistry */
    private $keyRegistry;

    public function __construct(
        AuthenticationServiceInterface $authService,
        KeyRegistryInterface $keyRegistry
    )
    {
        $this->authService = $authService;
        $this->keyRegistry = $keyRegistry;

        $config = [
            'name' => 'Token',
            'description' => 'Authentification token',
            'fields' => ['token' => Type::string()],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                return $value;
            }
        ];
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function getQueriesOrMutations()
    {
        return [
            'login'  => [
                'type'        => $this,
                'description' => 'Returns a jason web token according to the provide credentials. ' .
                'If no credentials are given, a token for anonymous login is returned.',
                'args'        => [
                    'username' => Type::string(),
                    'password' => Type::string(),
                    'lang' => Type::string(),
                    'shopid' => Type::int()
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function getQueryOrMutationHandlers()
    {
        return [
            'login' => function ($value, $args, $context, ResolveInfo $info) {
                /** @var AppContext $context */
                $tokenRequest = new TokenRequest();
                $tokenRequest->setUsername($args['username'] ? $args['username'] : '');
                $tokenRequest->setPassword($args['password'] ? $args['password'] : '');
                $tokenRequest->setLang($args['lang'] ? $args['lang'] : $context->getDefaultShopLanguage());
                $tokenRequest->setShopid($args['shopid'] ? $args['shopid'] : $context->getDefaultShopId());
                if ($context->hasAuthToken()) {
                    $tokenRequest->setCurrentToken($context->getToken());
                }
                $token = $this->authService->getToken($tokenRequest);
                $signatureKey = $this->keyRegistry->getSignatureKey();
                return $token->getJwt($signatureKey);
            }
        ];
    }
}
