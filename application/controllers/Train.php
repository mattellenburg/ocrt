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
        $this->load->model('point_model');
        $this->load->model('point_pending_model');
    }

    public function index() {
        $headerdata = new stdClass();
        $data = new stdClass();
        $headerdata->pageinformation = 'Select an obstacle to view relevant exercises.  Select an exerciese to view appropriate locations.';

        $data->exercises = $this->keyword_model->get_exercises();

        $obstacles = array();
        foreach ($this->keyword_model->get_obstacles() as $obstacle) {
            $exercises = array ();
            foreach($this->obstaclesexercises_model->get_obstaclesexercises_byobstacleid($obstacle->id) as $obstacleexercise) {
                $exercise = $this->keyword_model->get_keyword($obstacleexercise->exerciseid)[0]->keyword;
                array_push($exercises, '<a href="'.base_url('index.php/explore/index/').'?keyword='.$exercise.'">'.$exercise.'</a>');
            }
            $obstacles['<a>'.$obstacle->keyword.'</a>'] = $exercises;
        }        
        $data->obstacles = $obstacles;
        $data->points = $this->point_model->get_training_points(39.0353576, -77.12402559999998);
        
        $this->load->view('header', $headerdata);
        $this->load->view('train', $data);
        $this->load->view('footer');	
    }
}
