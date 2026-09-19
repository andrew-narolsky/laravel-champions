<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class SlugRedirectException extends RuntimeException
{
    public function __construct(public Model $model, public string $routeName)
    {
        parent::__construct('Redirecting to the current slug.');
    }
}
