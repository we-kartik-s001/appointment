<?php

use VaahCms\Modules\Appointments\Http\Controllers\Backend\PatientsController;

Route::group(
    [
        'prefix' => 'backend/appointments/patients',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [PatientsController::class, 'getAssets'])
        ->name('vh.backend.appointments.patients.assets');
    /**
     * Get List
     */
    Route::get('/', [PatientsController::class, 'getList'])
        ->name('vh.backend.appointments.patients.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [PatientsController::class, 'updateList'])
        ->name('vh.backend.appointments.patients.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [PatientsController::class, 'deleteList'])
        ->name('vh.backend.appointments.patients.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [PatientsController::class, 'fillItem'])
        ->name('vh.backend.appointments.patients.fill');

    /**
     * Create Item
     */
    Route::post('/', [PatientsController::class, 'createItem'])
        ->name('vh.backend.appointments.patients.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [PatientsController::class, 'getItem'])
        ->name('vh.backend.appointments.patients.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [PatientsController::class, 'updateItem'])
        ->name('vh.backend.appointments.patients.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [PatientsController::class, 'deleteItem'])
        ->name('vh.backend.appointments.patients.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [PatientsController::class, 'listAction'])
        ->name('vh.backend.appointments.patients.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [PatientsController::class, 'itemAction'])
        ->name('vh.backend.appointments.patients.item.action');

    //---------------------------------------------------------

});
