<?php

namespace src\dao;

enum LocationType: string
{
    case ON_SITE = 'on-site';
    case HYBRID = 'hybrid';
    case REMOTE = 'remote';
}
