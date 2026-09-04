<?php

namespace App\Enums;

enum CollegeType: string
{
    case GOVERNMENT = 'government';
    case PRIVATE = 'private';
    case SELF_FINANCED = 'self financed';
}
