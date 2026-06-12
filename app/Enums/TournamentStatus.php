<?php

namespace App\Enums;

enum TournamentStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Closed = 'closed';
}
