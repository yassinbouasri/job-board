<?php

declare(strict_types=1);


namespace App\Service;

use App\Enums\ExperienceEnum;
use App\Enums\JobTypeEnum;
use Symfony\Component\HttpFoundation\Request;

class Filter
{
    /**
     * @param  Request  $request
     * @return JobTypeEnum|null
     */
    public static function getTypeEnum(Request $request): ?JobTypeEnum
    {
        $queryParams = $request->query->all();

        $selectedValue = $queryParams['job_type_form']['jobType'] ?? null;

        return $selectedValue ? JobTypeEnum::from($selectedValue) : null;
    }

    public static function getExperienceEnum(Request $request): ?ExperienceEnum
    {
        $queryParams = $request->query->all();

        $selectedValue = $queryParams['experience_form']['experience'] ?? null;

        return $selectedValue ? ExperienceEnum::from($selectedValue) : null;
    }
}