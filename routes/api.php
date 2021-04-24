<?php

use FastRoute\RouteCollector;

return function(RouteCollector $r) {
    $r->addRoute('GET', '/users', [\Ports\Http\Controller\IndexController::class, 'index']);
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
};