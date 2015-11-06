<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
	
    public function create_user($email, $password) {
        $data = array(
            'email'      => $email,
            'password'   => $this->hash_password($password),
            'created_at' => date('Y-m-j H:i:s'),
        );

        return $this->db->insert('users', $data);
    }
	
    public function resolve_user_login($email, $password) {
        $this->db->select('password');
        $this->db->from('users');
        $this->db->where('email', $email);
        $hash = $this->db->get()->row('password');

        return $this->verify_password_hash($password, $hash);
    }
	
    public function get_user_id_from_email($email) {
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('email', $email);

        return $this->db->get()->row('id');
    }
	
    public function get_user($user_id) {
        $this->db->from('users');
        $this->db->where('id', $user_id);
        return $this->db->get()->row();
    }
	
    private function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
	
    private function verify_password_hash($password, $hash) {
        return password_verify($password, $hash);
    }	
}
