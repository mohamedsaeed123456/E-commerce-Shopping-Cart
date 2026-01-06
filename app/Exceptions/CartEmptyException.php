<?php

namespace App\Exceptions;

use Exception;

class CartEmptyException extends Exception
{
    protected $message = 'Your cart is empty';
}
