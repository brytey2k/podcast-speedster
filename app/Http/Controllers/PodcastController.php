<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PodcastCache;

class PodcastController extends Controller
{
    public function __invoke(): string
    {
        return PodcastCache::query()->latest()->first()->content;
    }
}
