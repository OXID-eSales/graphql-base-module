<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

use Firebase\JWT\JWT;
use OxidEsales\GraphQl\Exception\InsufficientData;

class Token
{
    const ALGORITHM = 'HS512';

    private $jwtObject;

    public function __construct(int $expiryDays=31)
    {
        $this->jwtObject = new \stdClass();
        $this->jwtObject->sub = null;
        $this->jwtObject->iat = null;
        $this->jwtObject->jti = $this->generateTokenKey();
        $this->jwtObject->iss = time();
        $this->jwtObject->aud = null;
        $this->jwtObject->exp = $this->jwtObject->iss + $expiryDays * 24 * 60 * 60;
        $this->jwtObject->data = new \stdClass();
        $this->jwtObject->data->lang = null;
        $this->jwtObject->data->shopId = null;
        $this->jwtObject->data->userName = null;
        $this->jwtObject->data->userGroup = null;
    }

    public function getJwt(string $signatureKey): string
    {
        $this->verifyData();

        return JWT::encode(
            json_decode(json_encode($this->jwtObject), true), //Data to be encoded in the JWT
            $signatureKey, // The signing key
            $this::ALGORITHM     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
    }

    public function setJwt(string $jwt, string $signatureKey)
    {
        $this->jwtObject = JWT::decode($jwt, $signatureKey, [$this::ALGORITHM]);
    }

    public function verifyData()
    {
        if (! $this->jwtObject->sub) {
            throw new InsufficientData("Missing subject data.");
        }
        if (! $this->jwtObject->iat) {
            throw new InsufficientData("Missing shop url.");
        }
        if (! $this->jwtObject->aud) {
            throw new InsufficientData("Missing shop url.");
        }
        if (! $this->jwtObject->data->lang) {
            throw new InsufficientData("Missing language.");
        }
        if ($this->jwtObject->data->shopid === null) {
            throw new InsufficientData("Missing shop id.");
        }
        if (! $this->jwtObject->data->userGroup) {
            throw new InsufficientData("Missing user group.");
        }
    }

    public function generateTokenKey()
    {
        return base64_encode(openssl_random_pseudo_bytes(16));
    }

    public function getKey()
    {
        return $this->jwtObject->jti;
    }

    public function setKey(string $key)
    {
        $this->jwtObject->jti = $key;
    }

    /**
     * @return null|string
     */
    public function getSubject()
    {
        return $this->jwtObject->sub;
    }

    /**
     * @param string|null $subject
     */
    public function setSubject($subject)
    {
        $this->jwtObject->sub = $subject;
    }

    /**
     * @return int
     */
    public function getIssueDate(): int
    {
        return $this->jwtObject->iss;
    }

    /**
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->jwtObject->jti;
    }

    /**
     * @return int
     */
    public function getExpiryDate()
    {
        return $this->jwtObject->exp;
    }

    /**
     * @return null|string
     */
    public function getIssuer()
    {
        return $this->jwtObject->iat;
    }

    /**
     * @return null|string
     */
    public function getAudience()
    {
        return $this->jwtObject->aud;
    }


    /**
     * @param string|null $shopUrl
     */
    public function setShopUrl($shopUrl)
    {
        $this->jwtObject->iat = $shopUrl;
        $this->jwtObject->aud = $shopUrl;
    }

    /**
     * @return null|string
     */
    public function getLang()
    {
        return $this->jwtObject->data->lang;
    }

    /**
     * @param string|null $lang
     */
    public function setLang($lang)
    {
        $this->jwtObject->data->lang = $lang;
    }

    /**
     * @return null|int
     */
    public function getShopid()
    {
        return $this->jwtObject->data->shopid;
    }

    /**
     * @param int|null $shopid
     */
    public function setShopid($shopid)
    {
        $this->jwtObject->data->shopid = $shopid;
    }

    /**
     * @return null|string
     */
    public function getUserGroup()
    {
        return $this->jwtObject->data->userGroup;
    }

    /**
     * @param null|string $userGroup
     */
    public function setUserGroup($userGroup)
    {
        $this->jwtObject->data->userGroup = $userGroup;
    }

    /**
     * @return null|string
     */
    public function getUserName()
    {
        return $this->jwtObject->data->userName;
    }

    /**
     * @param null|string $userName
     */
    public function setUserName($userName)
    {
        $this->jwtObject->data->userName = $userName;
    }
}
