<?php

namespace App\Enums;

enum ContactMessageCategory: string
{
    case GENERAL = 'general';
    case SUPPORT = 'support';
    case FEEDBACK = 'feedback';
    case REPORT = 'report';
    case OTHER = 'other';
}
