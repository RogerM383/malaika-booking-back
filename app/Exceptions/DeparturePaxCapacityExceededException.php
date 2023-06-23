<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class DeparturePaxCapacityExceededException extends Exception implements HttpExceptionInterface
{
    #[Pure] public function __construct()
    {
        $message = 'Departure pax capacity exceeded';
        parent::__construct($message);
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        parent::context();
        /*return array_merge(parent::context(), [
            'user_id' => Auth::id(),
        ]);*/
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
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
        return ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * Returns response headers.
     */
    public function getHeaders(): array
    {
        return [];
    }
}
