<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/app.php';

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
$resolver = new HttpKernel\Controller\ControllerResolver();

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
$dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

$listener = new HttpKernel\EventListener\ExceptionListener('Calendar\\Controller\\ErrorController::exceptionAction');
$dispatcher->addSubscriber($listener);

$dispatcher->addSubscriber(new Simplex\StringResponseListener());

$framework = new Simplex\Framework($dispatcher, $resolver);

$framework->handle($request)->send();