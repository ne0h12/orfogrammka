<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Entity;

final class State
{
    const ESTIMATED_SUCCESS = 'ESTIMATED_SUCCESS';
    const WAITING_CHECK     = 'WAITING_CHECK';
    const CHECKING          = 'CHECKING';
    const CHECKED_SUCCESS   = 'CHECKED_SUCCESS';
}