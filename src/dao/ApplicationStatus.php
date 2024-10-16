<?php

namespace src\dao;

enum ApplicationStatus: string
{
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case WAITING = 'waiting';
}
