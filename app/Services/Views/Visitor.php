<?php

use CyrildeWit\EloquentViewable\Contracts\Visitor;
$this->app->bind(
    Visitor::class,
    \App\Services\Views\Visitor::class
);