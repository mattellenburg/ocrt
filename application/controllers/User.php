<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct() {
            parent::__construct();
            $this->load->library(array('session'));
            $this->load->helper(array('url'));
            $this->load->model('user_model');
    }
		
    public function register() {
        $data = new stdClass();

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');

        if ($this->form_validation->run() === false) {
            $this->load->view('header');
            $this->load->view('register', $data);
            $this->load->view('footer');
        } 
        else {
            $email    = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->user_model->create_user($email, $password)) {
                $this->load->view('header');
                $this->load->view('home', $data);
                $this->load->view('footer');
            } 
            else {
                $data->error = 'There was a problem creating your new account. Please try again.';

                $this->load->view('header');
                $this->load->view('register', $data);
                $this->load->view('footer');
            }
        }
    }

    public function login() {
        $data = new stdClass();

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('header');
            $this->load->view('login');
            $this->load->view('footer');
        } 
        else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->user_model->resolve_user_login($email, $password)) {
                $user_id = $this->user_model->get_user_id_from_email($email);
                $user    = $this->user_model->get_user($user_id);

                $_SESSION['user_id']      = (int)$user->id;
                $_SESSION['logged_in']    = (bool)true;
                $_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
                $_SESSION['is_admin']     = (bool)$user->is_admin;

                $this->load->view('header');
                $this->load->view('home', $data);
                $this->load->view('footer');
            } 
            else {
                $data->error = 'Wrong email or password.';

                $this->load->view('header');
                $this->load->view('login', $data);
                $this->load->view('footer');
            }
        }
    }

    public function logout() {
        $data = new stdClass();

        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }

            $this->load->view('header');
            $this->load->view('home', $data);
            $this->load->view('footer');
        } 
        else {
            redirect('/');
        }
    }	
}
