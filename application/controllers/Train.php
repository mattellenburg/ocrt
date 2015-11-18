<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Train extends CI_Controller {
    public function __construct() {		
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('utilities');
        $this->load->library('form_validation');
        $this->load->model('keyword_model');
        $this->load->model('obstaclesexercises_model');
    }

    public function index() {
        $data = new stdClass();
        $data->message = '';

        $data->exercises = $this->keyword_model->get_exercises();

        $obstacles = array();
        foreach ($this->keyword_model->get_obstacles() as $obstacle) {
            $exercises = array ();
            foreach($this->obstaclesexercises_model->get_obstaclesexercises_byobstacleid($obstacle->id) as $obstacleexercise) {
                array_push($exercises, $this->keyword_model->get_keyword($obstacleexercise->exerciseid)[0]->keyword);
            }
            $obstacles['<a>'.$obstacle->keyword.'</a>'] = $exercises;
        }        
        $data->obstacles = $obstacles;

        $this->load->view('header', $data);
        $this->load->view('train', $data);
        $this->load->view('footer');	
    }
}
