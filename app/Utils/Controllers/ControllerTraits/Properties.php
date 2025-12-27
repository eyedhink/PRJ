<?php

namespace App\Utils\Controllers\ControllerTraits;

use App\Utils\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

trait Properties
{
    public string $model = Model::class;
    public string $resource = BaseResource::class;
    public array $loadRelations = [];
    public bool $ability_system = false;
    public string $ability_guard = "admin";
    public string $ability_prefix = "";
    public array $ability_system_blacklist = [];
    public array $validation = [];
    public array $validation_create = [];
    public array $validation_index = [];
    public array $validation_update = [];
    public array $validation_extensions = [];
    public array $custom_kws = [];
    public array $selection_query_blacklist = [];
    public array $selection_query_replace = [];
    public array $match_ids = [];
    public array $access_checks = [];
}
