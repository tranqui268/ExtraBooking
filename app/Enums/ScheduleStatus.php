<?php

namespace App\Enums;

enum ScheduleStatus : string
{
    case Available = 'available';
    case Booked = 'booked';
    case Unavailable = 'unavailable';

}
