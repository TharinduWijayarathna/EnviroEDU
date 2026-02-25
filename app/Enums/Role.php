<?php

namespace App\Enums;

enum Role: string
{
    case SchoolAdmin = 'admin';
    case Teacher = 'teacher';
    case Student = 'student';
    case Parent = 'parent';
}
