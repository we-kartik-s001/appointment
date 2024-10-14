<?php

use VaahCms\Modules\Appointments\Http\Controllers\Backend\DoctorsController;

Route::group(
    [
        'prefix' => 'backend/appointments/doctors',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [DoctorsController::class, 'getAssets'])
        ->name('vh.backend.appointments.doctors.assets');
    /**
     * Get List
     */
    Route::get('/', [DoctorsController::class, 'getList'])
        ->name('vh.backend.appointments.doctors.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [DoctorsController::class, 'updateList'])
        ->name('vh.backend.appointments.doctors.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [DoctorsController::class, 'deleteList'])
        ->name('vh.backend.appointments.doctors.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [DoctorsController::class, 'fillItem'])
        ->name('vh.backend.appointments.doctors.fill');

    /**
     * Create Item
     */
    Route::post('/', [DoctorsController::class, 'createItem'])
        ->name('vh.backend.appointments.doctors.create');

    /**
     * Get Distinct Doctor Specialization
     */
    Route::get('/specialization',  [DoctorsController::class, 'getSpecialization'])
        ->name('vh.backend.appointments.doctors.specialization.read');;

    /**
     * Get Item
     */
    Route::get('/{id}', [DoctorsController::class, 'getItem'])
        ->name('vh.backend.appointments.doctors.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [DoctorsController::class, 'updateItem'])
        ->name('vh.backend.appointments.doctors.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [DoctorsController::class, 'deleteItem'])
        ->name('vh.backend.appointments.doctors.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [DoctorsController::class, 'listAction'])
        ->name('vh.backend.appointments.doctors.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [DoctorsController::class, 'itemAction'])
        ->name('vh.backend.appointments.doctors.item.action');

    //---------------------------------------------------------

    Route::post('/testing/upload',[DoctorsController::class,'bulkUpload'])
        ->name('vh.backend.appointments.doctors.bulkupload.action');

    Route::get('/exportDoctors/list',[DoctorsController::class,'exportDoctors'])
        ->name('vh.backend.appointments.doctors.bulkexport.action');

    Route::post('/importDoctors/list',[DoctorsController::class,'importDoctors']);
});
