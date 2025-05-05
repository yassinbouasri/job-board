<?php

namespace App\Enums;

enum JobTypeEnum: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case REMOTE = 'remote';
}
