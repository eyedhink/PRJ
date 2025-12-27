<?php

namespace App\Utils\Controllers\ControllerTraits;

use App\Utils\Exceptions\CustomException;
use App\Utils\Functions\FunctionUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait Destroy
{
    /**
     * @throws CustomException
     */
    public
    function destroy($kw, Request $request): JsonResponse
    {
        if ($this->ability_system && (!isset($this->ability_system_blacklist) || !array_search('store', $this->ability_system_blacklist)) && !FunctionUtils::isAuthorized($request->user($this->ability_guard), $this->ability_prefix . "-destroy")) {
            throw new CustomException("Access Denied");
        }
        $custom_kw = array_search("destroy", array_keys($this->custom_kws));
        if ($this->access_checks) {
            foreach ($this->access_checks as $name => $check) {
                $result = $check($request, [], 'destroy:' . $kw . ":" . "id");
                if (!$result) {
                    throw new CustomException("Access Denied");
                }
            }
        }
        $query = $this->selection_query_with_trashed != null && !in_array('destroy', $this->selection_query_blacklist) ? ($this->selection_query_with_trashed)($request) : ($this->selection_query != null ? ($this->selection_query)($request) : $this->model::query());
        foreach ($this->selection_query_replace as $key => $value) {
            if ($key == 'destroy') {
                $query = $value($request);
            }
        }
        $query->firstWhere(($custom_kw || $custom_kw === 0) ? $this->custom_kws["destroy"] : "id", $kw)->forceDelete();
        return response()->json(["message" => last(explode('\\', get_class(new $this->model()))) . " permanently deleted successfully"]);
    }
}
