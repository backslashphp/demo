<?php

declare(strict_types=1);

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Demo\Feature\Admin\Http\DemoHandler;
use Demo\Feature\Admin\Http\PurgeProjectionsHandler;
use Demo\Feature\Admin\Http\RebuildProjectionsHandler;
use Demo\Feature\Admin\Http\RebuildProjectionsToHandler;
use Demo\Feature\Admin\Http\ViewEventsHandler;
use Demo\Feature\Admin\Http\ViewProjectionStoreHandler;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseCapacity\Http\ChangeCourseCapacityHandler;
use Demo\Feature\CourseDefinition\Exception\CourseIdAlreadyUsedException;
use Demo\Feature\CourseDefinition\Exception\CourseNotDefinedException;
use Demo\Feature\CourseDefinition\Exception\InvalidCourseIdException;
use Demo\Feature\CourseDefinition\Http\DefineCourseHandler;
use Demo\Feature\CourseSubscription\Exception\CourseAtFullCapacityException;
use Demo\Feature\CourseSubscription\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Feature\CourseSubscription\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Feature\CourseSubscription\Exception\StudentNotSubscribedToCourseException;
use Demo\Feature\CourseSubscription\Http\SubscribeStudentHandler;
use Demo\Feature\CourseSubscription\Http\UnsubscribeStudentHandler;
use Demo\Feature\StudentRegistration\Exception\InvalidStudentIdException;
use Demo\Feature\StudentRegistration\Exception\StudentIdAlreadyUsedException;
use Demo\Feature\StudentRegistration\Exception\StudentNotRegisteredException;
use Demo\Feature\StudentRegistration\Http\RegisterStudentHandler;
use Demo\Feature\CourseView\Http\CourseViewHandler;
use Demo\Feature\CourseListView\Http\CourseListViewHandler;
use Demo\Feature\StudentView\Http\StudentViewHandler;
use Demo\Feature\StudentListView\Http\StudentListViewHandler;
use Demo\Infrastructure\SharedModeMiddleware;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestHandler;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../bootstrap.php';

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->getRouteCollector()->setDefaultInvocationStrategy(new RequestHandler(true));
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
if (getenv('APP_SHARED')) {
    $app->add($container->get(SharedModeMiddleware::class));
}

// Error middleware: domain exceptions → 422 JSON
$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$errorMiddleware->setDefaultErrorHandler(function (ServerRequestInterface $request, Throwable $e, bool $displayErrorDetails) {
    if ($e instanceof ProjectionNotFoundException) {
        return new JsonResponse(['error' => 'Projection not found.'], 404);
    }

    $message = match ($e::class) {
        CourseAtFullCapacityException::class => 'Course is at full capacity.',
        CourseIdAlreadyUsedException::class => 'Course ID is already used.',
        CourseNotDefinedException::class => 'Unknown course.',
        InvalidCourseCapacityException::class => 'Capacity must be an integer greater than 0.',
        InvalidCourseIdException::class => 'ID must be an integer.',
        InvalidStudentIdException::class => 'ID must be an integer.',
        StudentAlreadySubscribedToCourseException::class => 'Student is already subscribed to course.',
        StudentMaximumSubscriptionsReachedException::class => 'Student cannot subscribe to more than 3 courses.',
        StudentIdAlreadyUsedException::class => 'Student ID is already used.',
        StudentNotRegisteredException::class => 'Unknown student.',
        StudentNotSubscribedToCourseException::class => 'Student is not subscribed to course.',
        default => $e->getMessage() ?: $e::class,
    };

    return new JsonResponse(['error' => $message], 422);
});

// SPA
$app->get('/', fn (ServerRequestInterface $request) => new HtmlResponse(file_get_contents(__DIR__ . '/index.html')));

// API — views
$app->get('/api/students', StudentListViewHandler::class);
$app->get('/api/students/{id}', StudentViewHandler::class);
$app->get('/api/courses', CourseListViewHandler::class);
$app->get('/api/courses/{id}', CourseViewHandler::class);

// API — commands
$app->post('/api/register-student', RegisterStudentHandler::class);
$app->post('/api/define-course', DefineCourseHandler::class);
$app->post('/api/change-capacity', ChangeCourseCapacityHandler::class);
$app->post('/api/subscribe', SubscribeStudentHandler::class);
$app->post('/api/unsubscribe', UnsubscribeStudentHandler::class);

// API — admin
$app->post('/api/admin/demo', DemoHandler::class);
$app->post('/api/admin/rebuild-projections', RebuildProjectionsHandler::class);
$app->post('/api/admin/rebuild-projections-to/{sequence}', RebuildProjectionsToHandler::class);
$app->post('/api/admin/purge-projections', PurgeProjectionsHandler::class);
$app->get('/api/admin/events', ViewEventsHandler::class);
$app->get('/api/admin/projections', ViewProjectionStoreHandler::class);

$app->run();
