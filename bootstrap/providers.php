<?php

use App\Providers\AppServiceProvider;
use App\Providers\PayDunyaServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [
    AppServiceProvider::class,
    RepositoryServiceProvider::class,
    PayDunyaServiceProvider::class,
];
