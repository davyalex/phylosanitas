<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Exception $e) {
            //
            if ($e->getPrevious() instanceof TokenMismatchException) {
               return redirect()->route('login-form');
            }
        });
    }
    
     public function render($request, Throwable $exception)
    {
        // Vérifier si le site est en mode maintenance
        if ($exception instanceof HttpException && $exception->getStatusCode() === 503) {
            return response()->view('maintenance', [], 503);
        }

        return parent::render($request, $exception);
    }
}
