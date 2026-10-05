<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth
{
    private $session;

    public function __construct()
    {
        $lava = lava_instance();
        $lava->call->library('session');
        $this->session = $lava->session;
    }

    public function check()
    {
        if (!$this->session->has_userdata('user_id')) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Unauthorized access. Please login.']);
            exit;
        }
    }

    public function login($user_id)
    {
        $this->session->sess_regenerate(true);
        $this->session->set_userdata('user_id', $user_id);
    }

    public function logout()
    {
        $this->session->unset_userdata('user_id');
    }

    public function user_id()
    {
        return $this->session->userdata('user_id');
    }
}
