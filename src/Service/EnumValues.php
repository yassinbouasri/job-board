<?php

declare(strict_types=1);


namespace App\Service;

use App\Enums\ExperienceEnum;
use App\Enums\JobTypeEnum;
use Symfony\Component\HttpFoundation\Request;

class EnumValues
{
    public static function getEnum(Request $request, $enumClass, string $formName, string $queryParam): mixed
    {
        $queryParams = $request->query->all();

        $selectedValue = $queryParams[$formName][$queryParam] ?? null;

        return $selectedValue ? $enumClass::from($selectedValue) : null;
    }
}