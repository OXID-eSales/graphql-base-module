<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 14.02.19
 * Time: 15:29
 */

namespace OxidEsales\GraphQl\Service;

interface EnvironmentServiceInterface
{

    public function getShopUrl(): string;

    public function getDefaultLanguage(): string;

    public function getDefaultShopId(): int;
}