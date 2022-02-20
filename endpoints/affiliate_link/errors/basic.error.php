<?php

class BasicError extends Exception {
    protected string $type;

    public function __construct(string $message, string $error_type) {
        parent::__construct($message);
        $this->type = $error_type;
    }

    public function ensure_response(): WP_REST_Response {
        $response = new WP_REST_Response(
            [
                'error_type' => $this->type,
                'error_message' => $this->message,
            ],
            400
        );

        return rest_ensure_response($response);
    }

    public function simple_ensure_response() {
        status_header(400);

        if (empty($this->message)) {
            print_r('URL inválida.');
        } else {
            print_r($this->message);
        }
        exit();
    }

    public function getErrorType(): string {
        return $this->type;
    }
}
