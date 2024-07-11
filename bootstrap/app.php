<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Your middleware configuration
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (AccessDeniedHttpException $e) {
            return response()->json([
                'message' => 'Access Denied. This action is unauthorized.'
            ], 403);
        });
        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'message' => 'Record not found.'
            ], 404);
        });
    })->create();


    // ->withExceptions(function (Exceptions $exceptions) {
    //     $exceptions->respond(function (Response $response, $request) {
    //         $errResponse =  $response->getStatusCode();
    //         $req = $request;
    //         if ($response->getStatusCode() === 404) {
    //             return response()->json([
    //                 'error' => 'Resource not found!!',
    //             ],404);
    //         }

    //        // return $response;
    //     });
    // })->create();

    // ->withExceptions(function (Exceptions $exceptions) {
    //     $exceptions->respond(function ( AccessDeniedHttpException $e, $request) {
    //         $x = $e;
    //         if ($request->wantsJson()) {
    //             return response()->json([
    //                 'error' => 'Entry for ' . str_replace('App', '', $e->getMessage()) . ' not found'],
    //                 404
    //             );
    //         }
    //     })
    //         ->respond(function (ModelNotFoundException $e, $request) {
    //             if ($request->wantsJson()) {
    //                 return response()->json([
    //                     'error' => $e->getMessage()],
    //                     404
    //                 );
    //             }
    //         });
    //     })->create();

    // ->withExceptions(function (Exceptions $exceptions) {
    //     $exceptions->stopIgnoring(AccessDeniedHttpException::class);
    //     $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
    //         return response()->json(['error' => 'Access Denied. This action is unauthorized.']);
    //     });
    //     $exceptions->stopIgnoring(ModelNotFoundException::class);
    //     $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
    //         return response()->json(['error' => 'Model not found.']);
    //     });
    // })->create();
    // ->withExceptions(function (Exceptions $exceptions) {
    //     $exceptions->stopIgnoring(AccessDeniedHttpException::class);
    //     $exceptions->stopIgnoring(ModelNotFoundException::class);
    //     $exceptions->render(function ($exception, Request $request) {
    //         if ($exception instanceof AccessDeniedHttpException) {
    //             return response()->json(['error' => 'Access Denied. This action is unauthorized.'], 403);
    //         } elseif ($exception instanceof ModelNotFoundException) {
    //             return response()->json(['error' => 'Model not found.'], 404);
    //         }
    //     });
    // })->create();

