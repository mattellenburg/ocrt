<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Race extends CI_Controller {
    public function __construct() {		
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index() {
        $data = new stdClass();
        $data->message = '';
        
        $this->load->view('header', $data);
        $this->load->view('race');
        $this->load->view('footer');	
    }
}
