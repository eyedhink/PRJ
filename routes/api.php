<?php

use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportControllerAdmin;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskControllerAdmin;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthorizeAbility;
use Illuminate\Support\Facades\Route;

Route::post("/user-login", [UserController::class, "login"]);

Route::post("/manager-login", [ManagerController::class, "login"]);

Route::middleware('auth:user')->group(function () {
    Route::post("/message-create", [MessageController::class, "store"])//        ->middleware('ability:message-create')
    ;
    Route::get("/messages", [MessageController::class, "index"])//        ->middleware('ability:message-index')
    ;
    Route::get("/message/{kw}", [MessageController::class, "show"])//        ->middleware('ability:message-show')
    ;
    Route::put("/edit-message/{kw}", [MessageController::class, "edit"])//        ->middleware('ability:message-update')
    ;
    Route::delete("/delete-message/{kw}", [MessageController::class, "destroy"])//        ->middleware('ability:message-delete')
    ;

    Route::post("/report-create", [ReportController::class, "store"]);
    Route::get("/reports", [ReportController::class, "index"]);
    Route::get("/report/{kw}", [ReportController::class, "show"]);
    Route::put("/edit-report/{kw}", [ReportController::class, "edit"]);
    Route::delete("/delete-report/{kw}", [ReportController::class, "destroy"]);

    Route::get("/reports-admin", [ReportControllerAdmin::class, "index"])
        ->middleware('ability:report-index');
    Route::get("/report-admin/{kw}", [ReportControllerAdmin::class, "show"])
        ->middleware('ability:report-show');

    Route::post("/task-create", [TaskController::class, "store"]);
    Route::get("/tasks", [TaskController::class, "index"]);
    Route::get("/task/{kw}", [TaskController::class, "show"]);
    Route::put("/edit-task/{kw}", [TaskController::class, "edit"]);
    Route::delete("/delete-task/{kw}", [TaskController::class, "destroy"]);

    Route::post("/task-create-admin", [TaskControllerAdmin::class, "store"])
        ->middleware('ability:task-create');
    Route::get("/tasks-admin", [TaskControllerAdmin::class, "index"])
        ->middleware('ability:task-index');
    Route::get("/task-admin/{kw}", [TaskControllerAdmin::class, "show"])
        ->middleware('ability:task-show');
    Route::put("/edit-task-admin/{kw}", [TaskControllerAdmin::class, "edit"])
        ->middleware('ability:task-update');
    Route::delete("/delete-task-admin/{kw}", [TaskControllerAdmin::class, "destroy"])
        ->middleware('ability:task-delete');

    Route::get("/slaves", [UserController::class, "index"]);
    Route::get("/slave/{kw}", [UserController::class, "show"]);
});

Route::middleware("auth:manager")->group(function () {
    Route::post("/user-apply-role/{id}", [UserController::class, "apply_role"]);
    Route::post("/user-create", [UserController::class, "store"]);
    Route::get("/users", [UserController::class, "indexAdmin"]);
    Route::get("/user/{kw}", [UserController::class, "showAdmin"]);

    Route::post("/role-create", [RoleController::class, "store"]);
    Route::get("/roles", [RoleController::class, "index"]);
    Route::get("/role/{kw}", [RoleController::class, "show"]);
    Route::put("/edit-role/{kw}", [RoleController::class, "edit"]);
    Route::delete("/delete-role/{kw}", [RoleController::class, "destroy"]);
});
