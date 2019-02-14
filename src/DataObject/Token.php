<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\DataObject;

use Firebase\JWT\JWT;
use OxidEsales\GraphQl\Exception\InsufficientTokenData;

class Token
{

    private $signatureKey;

    private $subject = null;
    private $issueDate = null;
    private $tokenId = null;
    private $expiryDate = null;
    private $shopUrl = null;
    private $lang = null;
    private $shopid = null;
    private $userGroup = null;

    public function __construct(string $signatureKey)
    {
        $this->signatureKey = $signatureKey;
    }

    public function getJwt(): string
    {
        $this->verifyData();

        if (!$this->tokenId) {
            $this->tokenId = $this->generateTokenKey();
        }

        if (!$this->issueDate) {
            $this->issueDate = time();
        }

        if (!$this->expiryDate) {
            $this->expiryDate = $this->issueDate + (365 * 24 * 60 * 60);
        }

        $tokenArray = [
            'sub'  => $this->subject,             //Subject
            'iat'  => $this->issueDate,          // Issued at: time when the token was generated
            'jti'  => $this->tokenId,            // Json Token Id: an unique identifier for the token
            'iss'  => $this->shopUrl,         // Issuer
            'aud'  => $this->shopUrl,         // Audience
            'exp'  => $this->expiryDate,            // Expire
            'data' => [                     // Data related to the signer use
                                            'lang'      => $this->lang,
                                            'shopid'    => $this->shopid,
                                            'usergroup' => $this->userGroup
            ]
        ];
        $jwt = JWT::encode(
            $tokenArray,      //Data to be encoded in the JWT
            $this->signatureKey, // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        return $jwt;
    }

    public function setJwt($jwt)
    {
        $tokenArray = JWT::decode($jwt, $this->signatureKey, ['HS512']);
        $this->subject = $tokenArray->sub;
        $this->issueDate = $tokenArray->iat;
        $this->tokenId = $tokenArray->jti;
        $this->shopUrl = $tokenArray->iss;
        $this->expiryDate = $tokenArray->exp;
        $data = $tokenArray->data;
        $this->lang = $data->lang;
        $this->shopid = $data->shopid;
        $this->userGroup = $data->usergroup;
    }

    public function verifyData()
    {
        if ($this->subject === null) {
            throw new InsufficientTokenData("Missing subject data.");
        }
        if ($this->shopUrl === null) {
            throw new InsufficientTokenData("Missing shop url.");
        }
        if ($this->lang === null) {
            throw new InsufficientTokenData("Missing language.");
        }
        if ($this->shopid === null) {
            throw new InsufficientTokenData("Missing shop id.");
        }
        if ($this->userGroup === null) {
            throw new InsufficientTokenData("Missing user group.");
        }
    }

    public function generateTokenKey()
    {
        return base64_encode(openssl_random_pseudo_bytes(16));
    }

    /**
     * @return null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param null $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return null
     */
    public function getIssueDate()
    {
        return $this->issueDate;
    }

    /**
     * @param null $issueDate
     */
    public function setIssueDate($issueDate)
    {
        $this->issueDate = $issueDate;
    }

    /**
     * @return null
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * @param null $tokenId
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;
    }

    /**
     * @return null
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param null $expiryDate
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;
    }

    /**
     * @return null
     */
    public function getShopUrl()
    {
        return $this->shopUrl;
    }

    /**
     * @param null $shopUrl
     */
    public function setShopUrl($shopUrl)
    {
        $this->shopUrl = $shopUrl;
    }

    /**
     * @return null
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param null $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return null
     */
    public function getShopid()
    {
        return $this->shopid;
    }

    /**
     * @param null $shopid
     */
    public function setShopid($shopid)
    {
        $this->shopid = $shopid;
    }

    /**
     * @return null
     */
    public function getUserGroup()
    {
        return $this->userGroup;
    }

    /**
     * @param null $userGroup
     */
    public function setUserGroup($userGroup)
    {
        $this->userGroup = $userGroup;
    }

}
