<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('user_model');
    }

    public function index() {
        $data = new stdClass();
        $data->message = '';

        $this->load->view('header', $data);
        $this->load->view('about');	
        $this->load->view('footer');
    }
}
