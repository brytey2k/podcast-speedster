<?php

declare(strict_types=1);

namespace App\Helpers\Traits;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * @phpstan-require-extends Command
 */
trait SendsConsoleOutputToLogs
{
    public function infoConsoleOutputAndLog(string $content, array $context = []): void
    {
        Log::info($content, $context);
        $this->info($content);
    }
}
