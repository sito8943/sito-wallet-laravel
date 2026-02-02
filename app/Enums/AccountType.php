<?php

namespace App\Enums;

enum AccountType: string
{
    case CASH = 'CASH';
    case BANK = 'BANK';
    case CREDIT = 'CREDIT';
}

