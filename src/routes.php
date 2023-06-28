<?php

$app->get('/', \EmployeeController::class . ':index');
$app->get('/employees/create', \EmployeeController::class . ':create')->setName('employees.create');
$app->get('/employees/list', \EmployeeController::class . ':list')->setName('employees.list');
$app->get('/employees/view/{id}', \EmployeeController::class . ':view')->setName('employees.view');
$app->get('/employees/edit/{id}', \EmployeeController::class . ':edit')->setName('employees.edit');
$app->put('/employees/update/{id}', \EmployeeController::class . ':update')->setName('employees.update');
$app->post('/employees/store', \EmployeeController::class . ':store')->setName('employees.store');
$app->delete('/employees/delete/{id}', \EmployeeController::class . ':delete')->setName('employees.delete');