<?php

namespace App\Enums\Admin;

enum AccessLevel: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
}
