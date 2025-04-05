<?php

declare(strict_types=1);

Illuminate\Support\Facades\Schedule::command('app:cache-podcast-content')
    ->everyTwoMinutes();
Illuminate\Support\Facades\Schedule::command('app:prune-stale-cached-podcast-content')
    ->everyThirtyMinutes();
