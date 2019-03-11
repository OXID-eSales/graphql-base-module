<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 27.02.19
 * Time: 11:17
 */

namespace OxidEsales\GraphQl\Utility;

interface LegacyWrapperInterface
{
    public function encodePassword(string $password, string $salt): string;

    public function createSalt(): string;

    public function createUid();
}