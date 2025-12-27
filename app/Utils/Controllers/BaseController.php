<?php

namespace App\Utils\Controllers;

use AllowDynamicProperties;
use App\Utils\Controllers\ControllerTraits\AIO;
use App\Utils\Controllers\ControllerTraits\Constructor;

#[AllowDynamicProperties] class BaseController extends Controller
{
    use AIO, Constructor;
}
