<?php

class URLParamsError extends BasicError {
    public function __construct(string $message) {
        parent::__construct($message, 'invalid_url_param');
    }
}
