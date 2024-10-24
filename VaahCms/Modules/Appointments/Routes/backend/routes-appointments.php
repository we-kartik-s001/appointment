<?php

use VaahCms\Modules\Appointments\Http\Controllers\Backend\AppointmentsController;

Route::group(
    [
        'prefix' => 'backend/appointments/appointments',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [AppointmentsController::class, 'getAssets'])
        ->name('vh.backend.appointments.appointments.assets');
    /**
     * Get List
     */
    Route::get('/', [AppointmentsController::class, 'getList'])
        ->name('vh.backend.appointments.appointments.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [AppointmentsController::class, 'updateList'])
        ->name('vh.backend.appointments.appointments.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [AppointmentsController::class, 'deleteList'])
        ->name('vh.backend.appointments.appointments.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [AppointmentsController::class, 'fillItem'])
        ->name('vh.backend.appointments.appointments.fill');

    /**
     * Create Item
     */
    Route::post('/', [AppointmentsController::class, 'createItem'])
        ->name('vh.backend.appointments.appointments.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [AppointmentsController::class, 'getItem'])
        ->name('vh.backend.appointments.appointments.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [AppointmentsController::class, 'updateItem'])
        ->name('vh.backend.appointments.appointments.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [AppointmentsController::class, 'deleteItem'])
        ->name('vh.backend.appointments.appointments.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [AppointmentsController::class, 'listAction'])
        ->name('vh.backend.appointments.appointments.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [AppointmentsController::class, 'itemAction'])
        ->name('vh.backend.appointments.appointments.item.action');

    //---------------------------------------------------------
    Route::get('/exportAppointments/list',[AppointmentsController::class,'exportAppointments'])
        ->name('vh.backend.appointments.doctors.bulkexport.action');

    Route::post('/importAppointments/list',[AppointmentsController::class,'importAppointments'])
        ->name('vh.backend.appointments.doctors.bulkimport.action');

    Route::get('/dataBaseHeaders/list',[AppointmentsController::class,'listDatabaseHeaders'])
        ->name('vh.backend.appointments.doctors.listDatabaseHeaders');

    Route::put('/csv/mapfields',[AppointmentsController::class,'mapFields'])
        ->name('vh.backend.appointments.doctors.mapFields');

    Route::get('/exportSample/csv',[AppointmentsController::class,'exportSampleCsv'])
        ->name('vh.backend.appointments.doctors.exportSampleCsv');;
});
