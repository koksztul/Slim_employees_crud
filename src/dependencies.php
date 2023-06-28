<?php
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../templates', [
        'cache' => false,
    ]);
    $view->addExtension(
        new \Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()
        )
    );
    return $view;
};

$container['EmployeeController'] = function ($container) {
    $view = $container->view;
    $db = $container->get('db');
    $flash = $container->get("flash");
    return new \App\controllers\EmployeeController($view, $db, $flash);
};

$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(
        'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};