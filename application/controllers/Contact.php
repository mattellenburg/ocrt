<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('user_model');
    }

    public function index() {
        $data = new stdClass();
        $data->message = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('body', 'Body', 'trim|required|min_length[6]');

        if ($this->form_validation->run() === false) {
            $this->load->view('header', $data);
            $this->load->view('contact');
            $this->load->view('footer');
        } 
        else {
            $this->load->library('email');

            $this->email->from($this->input->post('email'), $this->input->post('name'));
            $this->email->to('mattellenburg@ocrt4me.com'); 

            $this->email->subject('ocrt4me.com Feedback');
            $this->email->message($this->input->post('body'));	

            $this->email->send();
            
            $data->message = 'Your feedback has been received.';
            
            $this->load->view('header', $data);
            $this->load->view('contact');
            $this->load->view('footer');
        }
    }
}
