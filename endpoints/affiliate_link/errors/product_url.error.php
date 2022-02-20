<?php

class ProductURLError extends BasicError {
    public function __construct(string $message) {
        parent::__construct($message, 'invalid_product_url');
    }
}
