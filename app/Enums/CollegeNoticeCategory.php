<?php

namespace App\Enums;

enum CollegeNoticeCategory: string
{
    case EXAM = 'exam';
    case RESULT = 'result';
    case ADMISSION = 'admission';
    case SCHOLARSHIP = 'scholarship';
    case SPORT = 'sports';
    case OTHERS = 'others';
}
