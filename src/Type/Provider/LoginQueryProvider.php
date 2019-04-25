<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\Provider;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Service\PermissionsServiceInterface;
use OxidEsales\GraphQl\Type\BaseType;
use OxidEsales\GraphQl\Type\ObjectType\LoginType;

/**
 * Class LoginType
 *
 * @package OxidEsales\GraphQl\Type\Provider
 */
class LoginQueryProvider implements QueryProviderInterface
{

    /** @var AuthenticationServiceInterface $authService */
    private $authService;

    /** @var  KeyRegistryInterface $keyRegistry */
    private $keyRegistry;

    /** @var  PermissionsServiceInterface $permissionsService */
    private $permissionsService;

    /** @var LoginType $loginType */
    private $loginType;

    public function __construct(
        AuthenticationServiceInterface $authService,
        KeyRegistryInterface $keyRegistry,
        PermissionsServiceInterface $permissionsService,
        LoginType $loginType
    )
    {
        $this->authService = $authService;
        $this->keyRegistry = $keyRegistry;
        $this->permissionsService = $permissionsService;
        $this->loginType = $loginType;
    }
        /**
     * @return array
     */
    public function getQueries()
    {
        return [
            'login'  => [
                'type'        => $this->loginType,
                'description' => 'Returns a jason web token according to the provide credentials. ' .
                'If no credentials are given, a token for anonymous login is returned.',
                'args'        => [
                    'username' => Type::string(),
                    'password' => Type::string(),
                    'lang' => Type::string(),
                    'shopid' => Type::int()
                ],
            ],
            'setlanguage'  => [
                'type'        => $this->loginType,
                'description' => 'Changes the language in the current auth token, signs it again ' .
                'and returns the changed token.',
                'args'        => [
                    'lang' => Type::nonNull(Type::string())
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function getQueryResolvers()
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
                    $tokenRequest->setCurrentToken($context->getAuthToken());
                }
                $token = $this->authService->getToken($tokenRequest);
                $signatureKey = $this->keyRegistry->getSignatureKey();
                return $token->getJwt($signatureKey);
            },
            'setlanguage' => function ($value, $args, $context, ResolveInfo $info) {
                /** @var AppContext $context */
                $token = $context->getAuthToken();
                $this->permissionsService->checkPermission($token, 'mayreaddata');
                $token->setLang($args['lang']);
                $signatureKey = $this->keyRegistry->getSignatureKey();
                return $token->getJwt($signatureKey);

            }
        ];
    }
}
