<?php

namespace App\Utils\Controllers\ControllerTraits;

trait AIO
{
    use Properties, MatchIds, Store, Index, Show, Edit, Delete, Restore, Destroy;
}
