<?php

namespace src\dao;

enum JobType: string
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case INTERNSHIP = 'internship';
}
