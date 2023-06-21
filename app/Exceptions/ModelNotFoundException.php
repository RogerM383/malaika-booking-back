<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ModelNotFoundException extends Exception implements HttpExceptionInterface
{
    #[Pure] public function __construct(Model $model, array|int $ids, string $msg = null)
    {
        $name = class_basename($model);
        $_ids = is_array($ids) ? join(', ', $ids) : $ids.' ';
        $message = $msg ?? $name.' with id '.$_ids.'doesen\'t exists';
        parent::__construct($message);
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        return parent::context();
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
