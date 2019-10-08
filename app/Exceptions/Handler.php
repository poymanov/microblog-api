<?php

namespace App\Exceptions;

use App\Dto\factories\ErrorResponseDtoFactory;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use InvalidArgumentException;

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
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $dto = ErrorResponseDtoFactory::buildNotFound();
            $status = Response::HTTP_NOT_FOUND;
        } else if ($exception instanceof NotFoundException) {
            $dto = ErrorResponseDtoFactory::buildNotFound();
            $status = Response::HTTP_NOT_FOUND;
        } else if ($exception instanceof AuthenticationException) {
            if ($request->url() == route('api.auth.logout')) {
                $dto = ErrorResponseDtoFactory::buildUnauthorizedLogout();
                $status = Response::HTTP_UNAUTHORIZED;
            } else {
                $dto = ErrorResponseDtoFactory::buildAccessDenied();
                $status = Response::HTTP_FORBIDDEN;
            }
        } else if ($exception instanceof AccessDeniedException) {
            $dto = ErrorResponseDtoFactory::buildAccessDenied();
            $status = Response::HTTP_FORBIDDEN;
        } else if ($exception instanceof InvalidArgumentException) {
            $dto = ErrorResponseDtoFactory::buildAccessDenied();
            $status = Response::HTTP_FORBIDDEN;
        } else if ($exception instanceof ValidationException) {
            $dto = ErrorResponseDtoFactory::buildValidationFailed($exception->getErrors());
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        } else {
            $dto = ErrorResponseDtoFactory::buildSomethingWentWrong([$exception->getMessage()]);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json($dto->toArray(), $status);
    }
}
