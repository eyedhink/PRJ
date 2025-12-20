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

Route::post("/user-login", [UserController::class, "login"]);

Route::post("/manager-login", [ManagerController::class, "login"]);

Route::middleware('auth:user')->group(function () {
    Route::post("/message-create", [MessageController::class, "store"])
        ->middleware(AuthorizeAbility::class . ':message-create');
    Route::get("/messages", [MessageController::class, "index"])
        ->middleware(AuthorizeAbility::class . ':message-index');
    Route::get("/message/{kw}", [MessageController::class, "show"])
        ->middleware(AuthorizeAbility::class . ':message-show');
    Route::put("/edit-message/{kw}", [MessageController::class, "edit"])
        ->middleware(AuthorizeAbility::class . ':message-update');
    Route::delete("/delete-message/{kw}", [MessageController::class, "destroy"])
        ->middleware(AuthorizeAbility::class . ':message-delete');

    Route::post("/report-create", [ReportController::class, "store"]);
    Route::get("/reports", [ReportController::class, "index"]);
    Route::get("/report/{kw}", [ReportController::class, "show"]);
    Route::put("/edit-report/{kw}", [ReportController::class, "edit"]);
    Route::delete("/delete-report/{kw}", [ReportController::class, "destroy"]);

    Route::get("/reports-admin", [ReportControllerAdmin::class, "index"])
        ->middleware(AuthorizeAbility::class . ':report-index');
    Route::get("/report-admin/{kw}", [ReportControllerAdmin::class, "show"])
        ->middleware(AuthorizeAbility::class . ':report-show');

    Route::post("/task-create", [TaskController::class, "store"]);
    Route::get("/tasks", [TaskController::class, "index"]);
    Route::get("/task/{kw}", [TaskController::class, "show"]);
    Route::put("/edit-task/{kw}", [TaskController::class, "edit"]);
    Route::delete("/delete-task/{kw}", [TaskController::class, "destroy"]);

    Route::post("/task-create-admin", [TaskControllerAdmin::class, "store"])
        ->middleware(AuthorizeAbility::class . ':task-create');
    Route::get("/tasks-admin", [TaskControllerAdmin::class, "index"])
        ->middleware(AuthorizeAbility::class . ':task-index');
    Route::get("/task-admin/{kw}", [TaskControllerAdmin::class, "show"])
        ->middleware(AuthorizeAbility::class . ':task-show');
    Route::put("/edit-task-admin/{kw}", [TaskControllerAdmin::class, "edit"])
        ->middleware(AuthorizeAbility::class . ':task-update');
    Route::delete("/delete-task-admin/{kw}", [TaskControllerAdmin::class, "destroy"])
        ->middleware(AuthorizeAbility::class . ':task-delete');
});

Route::middleware("auth:manager")->group(function () {
    Route::post("/user-apply-role/{id}", [UserController::class, "apply_role"]);
    Route::post("/user-create", [UserController::class, "store"]);

    Route::post("/role-create", [RoleController::class, "store"]);
    Route::get("/roles", [RoleController::class, "index"]);
    Route::get("/role/{kw}", [RoleController::class, "show"]);
    Route::put("/edit-role/{kw}", [RoleController::class, "edit"]);
    Route::delete("/delete-role/{kw}", [RoleController::class, "destroy"]);
});
