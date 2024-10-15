<?php

namespace src\dao;

enum UserRole: string
{
    case JOBSEEKER = 'jobseeker';
    case COMPANY = 'company';
}
