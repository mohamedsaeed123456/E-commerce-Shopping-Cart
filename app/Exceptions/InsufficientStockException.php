<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $message = 'Insufficient stock available';

    public function __construct(
        string $message = null,
        public ?int $availableStock = null,
        public ?string $productName = null
    ) {
        if ($message) {
            $this->message = $message;
        } elseif ($this->productName && $this->availableStock !== null) {
            $this->message = "Insufficient stock for {$this->productName}. Available: {$this->availableStock}";
        }

        parent::__construct($this->message);
    }
}
