<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Train extends CI_Controller {
    public function __construct() {		
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('route_model');
        $this->load->model('workout_model');
        $this->load->model('routewaypoints_model');
        $this->load->model('keyword_model');
        $this->load->model('obstaclesexercises_model');
        $this->load->model('point_model');
        $this->load->model('point_pending_model');
        $this->load->library('utilities');
    }

    public function index($mapview = 1, $zoom = 13, $latitude = NULL, $longitude = NULL, $pointid = NULL, $querykeyword = NULL) {
        $headerdata = new stdClass();
        $data = new stdClass();
        $headerdata->pageinformation = 'Use the map to create and save routes.  Select an obstacle to view relevant exercises.  Select an exerciese to view appropriate locations.';

        if ($this->input->post('submitroute', TRUE) == 'Submit Route') {
            $route = new stdClass();
            $route->waypoints = array();
            
            $i=1;
            foreach (explode(";", rtrim($this->input->post('route', TRUE), ";")) as $waypoint) {
                $wp = new stdClass();
                $wp->latitude = floatval(explode("," , $waypoint)[0]);
                $wp->longitude = floatval(explode("," , $waypoint)[1]);
                $wp->pointid = intval(explode("," , $waypoint)[2]);
                $wp->sortorder = $i;
                
                $i++;
                
                array_push($route->waypoints, $wp);
            }
            
            if ($this->routewaypoints_model->create_routewaypoints($this->route_model->create_route($this->input->post('routename', TRUE)), $route)) {
                $headerdata->message = 'Your route has been saved.';
            }
        }
        
        $routeid = 0;
        if ($this->input->post('submitloadroute', TRUE) == 'Load Route') {
            $routeid=$this->input->post('route', TRUE);
        }
        
        if ($this->input->post('submitworkout', TRUE) == 'Create Workout') {
            if ($this->workout_model->create_workout($this->input->post('pointid', TRUE), $this->input->post('workoutdescription', TRUE))) {
                $headerdata->message = 'Your workout has been created';
            }
            else { 
                $headerdata->message = 'There was a problem creating your workout';
            }
        }

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
        
        $filters = $this->utilities->build_filter_class($this->input->post('filterrating', TRUE), $this->input->post('filtersearch', TRUE), $this->input->post('filterkeywords', TRUE), $this->input->post('mysubmissions', TRUE));
        if ($querykeyword !== NULL) {
            $filters->keywords = array($this->keyword_model->get_keyword_by_keyword($querykeyword)[0]->id);
        }

        $points = $this->point_model->get_points($latitude, $longitude, $filters);
        $workouts = array();
        
        if (sizeof($points) > 0) {
            foreach($points as $point) {
                $workout = $this->workout_model->get_workouts($point['id']);
                array_push($workouts, $workout);
            }
        }

        $data->mapview = $mapview;
        $data->zoom = $zoom;
        $data->latitude = $latitude;
        $data->longitude = $longitude;
        $data->pointid = $pointid;
        $data->querykeyword = $querykeyword;
        $data->keywords = $this->keyword_model->get_keywords();
        $data->points_pending = $this->point_pending_model->get_points();
        $data->points = $points;
        $data->routes = $this->utilities->builddropdownarrayroute($this->route_model->get_routes($latitude, $longitude));
        $data->routewaypoints = $this->routewaypoints_model->get_routewaypoints($routeid);
        $data->workouts = $workouts;
        $data->filters = $this->utilities->get_filter_text($filters);

        $this->load->view('header', $headerdata);
        $this->load->view('train', $data);
        $this->load->view('footer');	
    }
}
