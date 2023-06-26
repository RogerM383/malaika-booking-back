<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws \Exception|Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->stopIgnoring(UnauthorizedException::class);
        $this->stopIgnoring(AuthorizationException::class);
        $this->stopIgnoring(AuthenticationException::class);
        $this->stopIgnoring(AccessDeniedException::class);

        $this->reportable(function (Throwable $e) {
            return false;
        });
    }

    /**
     * @Override
     * Determine if the exception handler response should be JSON.
     *
     * @param  Request  $request
     * @param Throwable $e
     * @return bool
     */
    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return parent::shouldReturnJson($request, $e) || $request->is("api/*");
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\Response|JsonResponse|Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response|JsonResponse|Response
    {

        //return parent::render($request, $e);

        if ($this->shouldReturnJson($request, $e)) {
            return $this->handleException($request, $e);
        } else {
            return parent::render($request, $e);
        }
    }

    /**
     * @param $request
     * @param \Exception $exception
     * @return $this|JsonResponse|\Illuminate\Http\Response|Response
     */
    public function handleException($request, Throwable $exception): \Illuminate\Http\Response|JsonResponse|Response|static
    {
        if ($exception instanceof ModelNotFoundException) {
            return $this->sendError('Model not found.', $this->convertExceptionToArray($exception), 404);
        }

        if ($exception instanceof UnauthorizedException ||
            $exception instanceof AuthorizationException ||
            $exception instanceof AuthenticationException ||
            $exception instanceof AccessDeniedException) {
            return $this->sendError('Unauithorized', $this->convertExceptionToArray($exception), 401);
        }

        if ($exception instanceof QueryException) {
            return $this->sendError('Query incorrecta', $this->convertExceptionToArray($exception), $this->getExceptionCode($exception));
        }

        if ($exception instanceof ValidationException) {
            return $this->sendError($this->toString($exception), $exception->validator->errors(), 422);
        }

        return $this->sendError($this->toString($exception), $this->convertExceptionToArray($exception), $this->getExceptionCode($exception));
    }

    private function getExceptionCode($exception, $code = 500)
    {
        return $this->isHttpException($exception) ? $exception->getStatusCode() : $code;
    }

    private function toString($exception)
    {
        if (method_exists($exception, 'toString')) {
            $exceptionStr = $exception->toString();
        } else if (method_exists($exception, 'getMessage') && App::environment(['local', 'staging', 'testing'])) {
            $exceptionStr = $exception->getMessage();
        } else {
            $exceptionStr = 'Unexpected Exception. Try later';
        }
        return $exceptionStr;
    }

    /**
     * Convert the given exception to an array.
     *
     * @param Throwable $e
     * @return array
     */
    protected function convertExceptionToArray(Throwable $e): array
    {
        $response = parent::convertExceptionToArray($e);

        if (method_exists($e, 'getData')) {
            $response['data'] = $e->getData();
        }

        return $response;
    }

    public function sendError($message, $exception, $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (is_array($exception) && isset($exception['data'])) {
            $response['data'] = $exception['data'];
        }

        if(App::environment(['local', 'staging'])){
            $response['error'] = $exception;
        }

        return response()->json($response, $code);
    }
}
