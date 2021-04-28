<?php

use Core\Router\Route;
use Core\Router\RouteCollection;
use Ports\Http\Controller\IndexController;

return function(RouteCollection $r) {
    $r->add(Route::get('/users', [IndexController::class, 'index'])->name('users.index'));
    $r->add(Route::get('/users/{id:\d+}', [IndexController::class, 'view'])->name('users.view'));

};