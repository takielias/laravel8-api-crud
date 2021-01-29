<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->is("api/*")) {
            $request->headers->set('Accept', 'application/json');
            if ($e instanceof \TypeError || $e instanceof \Error) {
                return ExceptionHandler::render($request, new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious()));
            } elseif ($e instanceof AuthenticationException) {
                return response()->json([
                    "success" => false,
                    'code' => 401,
                    'msg' => $e->getMessage()
                ], 401);
            } else {
                return ExceptionHandler::render($request, $e);
            }
        }
        return parent::render($request, $e);
    }
}
