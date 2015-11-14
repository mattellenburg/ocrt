<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Race extends CI_Controller {
    public function __construct() {		
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('table');
        $this->load->model('race_model');
    }

    public function index() {
        $data = new stdClass();
        $data->message = '';

        $this->table->set_heading('Race', 'Date', 'Loacation', 'Description');

        foreach($this->race_model->get_races() as $race) {
            $this->table->add_row($race->race, $race->date, $race->location, $race-> description);
        }

        $this->load->view('header', $data);
        $this->load->view('race', $data);
        $this->load->view('footer');	
    }
}
