<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class AppModelNotFoundException extends Exception implements HttpExceptionInterface
{
    #[Pure] public function __construct($id, $msg = null)
    {
        $message = $msg ?? 'App model with id '.$id.' doesen\'t exists';
        parent::__construct($message);
    }

    /**
     * Get the default context variables for logging
     */
    protected function context()
    {
        //
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // TODO: Mirar de poner el context, por ejemplo el ID de usuario
        Log::error($this->getMessage());
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response(/* ... */);
    }

    /**
     * Returns the status code.
     */
    public function getStatusCode(): int
    {
        return 404;
    }

    /**
     * Returns response headers.
     */
    public function getHeaders(): array
    {
        return [];
    }
}
