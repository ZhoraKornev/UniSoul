<?php

namespace App\Services;

use App\Contracts\ActionHandler;
use Illuminate\Support\Collection;

class HandlerProvider
{
    private Collection $handlers;

    public function __construct()
    {
        // Automatically resolve all registered handlers from the service container
        $this->handlers = collect(config('nutgram.handlers', []))
            ->map(fn (string $handlerClass) => app($handlerClass));
    }

    /**
     * Find and return the appropriate handler for the given action.
     *
     * @param  string  $botCallbackAction  The callback action to handle
     *
     * @throws \RuntimeException If no handler supports the action
     */
    public function provide(string $botCallbackAction): ActionHandler
    {
        $handler = $this->handlers->first(
            fn (ActionHandler $handler) => $handler->isSupport($botCallbackAction)
        );

        if (! $handler) {
            throw new \RuntimeException(
                "No handler found for action: {$botCallbackAction}"
            );
        }

        return $handler;
    }

    /**
     * Get all registered handlers.
     *
     * @return Collection<ActionHandler>
     */
    public function getHandlers(): Collection
    {
        return $this->handlers;
    }
}
