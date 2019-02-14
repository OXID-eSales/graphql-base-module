<?php declare(strict_types=1);

namespace OxidEsales\GraphQl\Service;

interface PasswordEncoderInterface
{

    public function encodePassword(string $password, string $salt): string;

}