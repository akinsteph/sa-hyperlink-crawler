<?php
/**
 * WordPress core class mocks for testing.
 */

// Mock WP_Error class
if ( ! class_exists( 'WP_Error' ) ) {
    class WP_Error {
        public $code;
        public $message;
        public $data;

        public function __construct( $code, $message, $data = array() ) {
            $this->code = $code;
            $this->message = $message;
            $this->data = $data;
        }
    }
}

// Mock WP_REST_Response class
if ( ! class_exists( 'WP_REST_Response' ) ) {
    class WP_REST_Response {
        protected $data;
        protected $status;

        public function __construct( $data, $status = 200 ) {
            $this->data = $data;
            $this->status = $status;
        }

        public function get_data() {
            return $this->data;
        }

        public function get_status() {
            return $this->status;
        }
    }
}

// Mock WP_REST_Request class
if ( ! class_exists( 'WP_REST_Request' ) ) {
    class WP_REST_Request {
        protected $params = array();
        protected $headers = array();

        public function get_param( $key ) {
            return isset( $this->params[$key] ) ? $this->params[$key] : null;
        }

        public function get_header( $key ) {
            return isset( $this->headers[$key] ) ? $this->headers[$key] : null;
        }
    }
}
