<?php

declare(strict_types=1);


namespace App\Service;

class EmailDomainChecker
{
    private const FREE_DOMAINS = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
        'aol.com', 'protonmail.com', 'icloud.com', 'zoho.com',
        'yandex.com', 'mail.com', 'gmx.com', 'fastmail.com',
        'tutanota.com', 'hey.com'
    ];

    public static function isFreeDomain(string $email): bool
    {
        $domain = self::extractDomain($email);
        return in_array($domain, self::FREE_DOMAINS, true);
    }

    private static function extractDomain(string $email): string
    {
        $parts = explode('@', $email);
        return strtolower(array_pop($parts));
    }
}