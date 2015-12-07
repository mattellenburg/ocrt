<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('user_model');
        $this->load->library('email');
    }
		
    public function register() {
        $headerdata = new stdClass();
        $headerdata->message = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');
        $this->form_validation->set_rules('runpace_minutes', 'Running Pace Minutes', 'integer|greater_than[-1]');
        $this->form_validation->set_rules('runpace_seconds', 'Running Pace Seconds', 'integer|less_than[60]|greater_than[-1]');

        if ($this->form_validation->run() === false) {
            $this->load->view('header', $headerdata);
            $this->load->view('register');
            $this->load->view('footer');
        } 
        else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $runpace = NULL;
            if ($this->input->post('runpace_minutes', NULL) !== '' && $this->input->post('runpace_seconds', NULL) !== '') {
                $runpace = number_format($this->input->post('runpace_minutes')) + number_format(number_format($this->input->post('runpace_seconds')) / 60, 2);
            }
            
            if ($this->user_model->create_user($email, $password, $runpace)) {    
                $this->email->from($this->input->post('email'));
                $this->email->to('mattellenburg@ocrt4me.com'); 

                $this->email->subject('ocrt4me.com Registration');
                $this->email->message($this->input->post('email').' has registered for an account.');	

                $this->email->send();
                
                $headerdata->message = 'Your registration has been received.';
                $this->load->view('header', $headerdata);
                $this->load->view('register');
                $this->load->view('footer');
            } 
            else {
                $this->email->from($this->input->post('email'));
                $this->email->to('mattellenburg@ocrt4me.com'); 

                $this->email->subject('ocrt4me.com Registration');
                $this->email->message($this->input->post('email').' was unable to register for an account.');	

                $this->email->send();

                $headerdata->error = 'There was a problem creating your new account. Please try again.';
                $this->load->view('header', $headerdata);
                $this->load->view('register');
                $this->load->view('footer');
            }
        }
    }

    public function profile() {
        $headerdata = new stdClass();
        $headerdata->message = '';
        $data = new stdClass();

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|min_length[6]|matches[password]');
        $this->form_validation->set_rules('runpace_minutes', 'Running Pace Minutes', 'integer|greater_than[-1]');
        $this->form_validation->set_rules('runpace_seconds', 'Running Pace Seconds', 'integer|less_than[60]|greater_than[-1]');

        if ($this->form_validation->run() === true) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $runpace = NULL;
            if ($this->input->post('runpace_minutes', NULL) !== '' && $this->input->post('runpace_seconds', NULL) !== '') {
                $runpace = number_format($this->input->post('runpace_minutes')) + number_format(number_format($this->input->post('runpace_seconds')) / 60, 2);
            }

            if ($this->user_model->update_user($email, $password, $runpace)) {    
                $headerdata->message = 'Your profile has been updated.';
            } 
            else {
                $headerdata->error = 'There was a problem updating your profile.';
            }
        }

        $user=$this->user_model->get_user($_SESSION['user_id']);
        $data->email = $user->email;
        $data->runpace_minutes = NULL;
        if ($user->runpace !== NULL) {
            $data->runpace_minutes = number_format($user->runpace);
            $data->runpace_seconds = 60 * (number_format($user->runpace, 2) - number_format($user->runpace));
        }
        
        $this->load->view('header', $headerdata);
        $this->load->view('profile', $data);
        $this->load->view('footer');
    }

    public function login() {
        $headerdata = new stdClass();
        $headerdata->message = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('header', $headerdata);
            $this->load->view('login');
            $this->load->view('footer');
        } 
        else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->user_model->resolve_user_login($email, $password)) {
                $user_id = $this->user_model->get_user_id_from_email($email);
                $user    = $this->user_model->get_user($user_id);

                $_SESSION['user_id'] = (int)$user->id;
                $_SESSION['logged_in'] = (bool)true;
                $_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
                $_SESSION['is_admin'] = (bool)$user->is_admin;

                $headerdata->message = '<span class="message">You have successfully logged into the site.</span>';
                
                $this->load->view('header', $headerdata);
                $this->load->view('footer');
            } 
            else {
                $data->error = 'Wrong email or password.';

                $this->load->view('header', $headerdata);
                $this->load->view('login');
                $this->load->view('footer');
            }
        }
    }

    public function logout() {
        $data = new stdClass();
        $data->message = '<span class="message">You have been logged out of the system.</span>';
        
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }

            $this->load->view('header', $data);
            $this->load->view('home');
            $this->load->view('footer');
        } 
        else {
            redirect('/');
        }
    }	
}
