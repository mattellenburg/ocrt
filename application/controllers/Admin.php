<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('point_model');
        $this->load->model('point_pending_model');
        $this->load->model('keyword_model');
        $this->load->model('race_model');
        $this->load->model('obstaclesexercises_model');
        $this->load->model('obstaclesraces_model');
        $this->load->library('table');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('utilities');
    }

    public function index() {
        $data = new stdClass();
        $data->message = '';

        if ($_SESSION['is_admin']) {
            $this->load->view('header', $data);
            $this->load->view('admin/index');
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }
    
    public function locations($action = NULL, $id = NULL) {
        $data = new stdClass();
        $data->message = '';

        if ($_SESSION['is_admin']) {
            if ($action == 'approve' && $id > 0) {
                $pending_point = $this->point_pending_model->get_point($id);
                $point = $this->point_model->get_point($pending_point->pointid);

                if (sizeof($point) > 0) {
                    if ($pending_point->delete == 1) {
                        if ($this->point_model->delete_point($pending_point->pointid)) {					
                            $this->point_pending_model->delete_point($id);				
                            $data->message = 'Location has been deleted.';
                        } 
                        else {				
                            $data->message = 'There was a problem deleting the location.';
                        }                        
                    }
                    else {
                        if ($this->point_model->update_point($pending_point->pointid, $pending_point->title, $pending_point->description, $pending_point->latitude, $pending_point->longitude, $pending_point->icon)) {					
                            $this->point_pending_model->delete_point($id);				
                            $data->message = 'Location has been updated.';
                        } 
                        else {				
                            $data->message = 'There was a problem updating the location.';
                        }
                    }
                }
                else {
                    if ($this->point_model->create_point($pending_point->title, $pending_point->description, $pending_point->latitude, $pending_point->longitude, $pending_point->icon)) {					
                        $this->point_pending_model->delete_point($id);				
                        $data->message = 'Location has been approved.';
                    } 
                    else {				
                        $data->message = 'There was a problem approving the location.';
                    }
                }
            }
            else if ($action == 'deny' && $id > 0) {
                $pending_point = $this->point_pending_model->get_point($id);

                if ($this->point_pending_model->delete_point($id)) {					                    				
                    $data->message = 'Location has been denied.';
                } 
                else {				
                    $data->message = 'There was a problem denying the location.';
                }
            }

            $data->points_pending = $this->point_pending_model->get_points();

            $this->table->set_heading('Pending Location ID', 'Original Location ID', 'User ID', 'Create Date', 'Title', 'Description', 'Icon', 'Map', 'Delete', 'Action');

            foreach($this->point_pending_model->get_points() as $point) {
                $action = '<a href="'.base_url('index.php/admin/locations/approve/'.$point->id).'">Approve</a>&nbsp;<a href="'.base_url('index.php/admin/locations/deny/'.$point->id).'">Deny</a>';
                
                $this->table->add_row($point->id, $point->pointid, $point->createdbyid, $point->createdate, $point->title, $point->description, $point->icon, '', $point->delete, $action);
            }

            $this->load->view('header', $data);
            $this->load->view('admin/locations');
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }
    
    public function keywords($id = NULL) {
        $data = new stdClass();
        $data->message = '';

        if ($_SESSION['is_admin']) {
            $this->form_validation->set_rules('keyword', 'Keyword', 'trim|required');

            if ($this->form_validation->run() === true) {
                if ($this->input->post('id') > 0) {
                    if ($this->keyword_model->update_keyword($this->input->post('id'), $this->input->post('keyword'), $this->input->post('exercise'), $this->input->post('obstacle'))) {           
                        redirect('/admin/keywords');
                    }
                    else {
                        $data->message = 'There was a problem updating the keyword';
                    }
                }
                else {
                    if ($this->keyword_model->create_keyword($this->input->post('keyword'), $this->input->post('exercise'), $this->input->post('obstacle'))) {           
                        $data->message = 'Keyword has been entered';
                    }
                    else {
                        $data->message = 'There was a problem entering the keyword';
                    }
                }
            }
            elseif ($id > 0) {
                $data->keyword = $this->keyword_model->get_keyword($id)[0];
            }

            $this->table->set_heading('ID', 'Keyword', 'Exercise', 'Obstacle');

            foreach($this->keyword_model->get_keywords() as $keyword) {
                $this->table->add_row('<a href="'.base_url('index.php/admin/keywords/'.$keyword->id).'">'.$keyword->id.'</a>', $keyword->keyword, $keyword->exercise, $keyword->obstacle);
            }

            $this->load->view('header', $data);
            $this->load->view('admin/keywords');
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }

    public function races($id = NULL) {
        $data = new stdClass();
        $data->message = '';

        if ($_SESSION['is_admin']) {
            $this->form_validation->set_rules('race', 'Race', 'trim|required');

            if ($this->form_validation->run() === true) {
                if ($this->input->post('id') > 0) {
                    if ($this->race_model->update_race($this->input->post('id'), $this->input->post('race'), $this->input->post('date'), $this->input->post('location'), $this->input->post('description'))) {           
                        redirect('/admin/races');
                    }
                    else {
                        $data->message = 'There was a problem updating the race';
                    }
                }
                else {
                    if ($this->race_model->create_race($this->input->post('race'), $this->input->post('date'), $this->input->post('location'), $this->input->post('description'))) {           
                        $data->message = 'Race has been entered';
                    }
                    else {
                        $data->message = 'There was a problem entering the race';
                    }
                }
            }
            elseif ($id > 0) {
                $data->race = $this->race_model->get_race($id)[0];
            }

            $this->table->set_heading('ID', 'Race', 'Date', 'Location', 'Description');

            foreach($this->race_model->get_races() as $race) {
                $this->table->add_row('<a href="'.base_url('index.php/admin/races/'.$race->id).'">'.$race->id.'</a>', $race->race, $race->date, $race->location, $race->description);
            }

            $this->load->view('header', $data);
            $this->load->view('admin/races');
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }

    public function obstaclesexercises($id = NULL) {
        $data = new stdClass();
        $data->message = '';
       
        if ($_SESSION['is_admin']) {
            if ($this->input->post('obstacle') > 0 && $this->input->post('exercise') > 0) {
                if ($this->obstaclesexercises_model->create_obstacleexercise($this->input->post('obstacle'), $this->input->post('exercise'))) {
                    $data->message = 'Obstacle/Exercise created';
                }
                else {
                    $data->message = 'There was a problem creating the obstacle/exercise';
                }    
            }
            else if ($id>0) {
                if ($this->obstaclesexercises_model->delete_obstacleexercise($id)) {
                    $data->message = 'Obstacle/Exercise deleted';
                }
                else {
                    $data->message = 'There was a problem deleting the obstacle/exercise';
                }
            }
            
            $this->table->set_heading('ID', 'ObstacleID', 'ExerciseID');
            foreach($this->obstaclesexercises_model->get_obstaclesexercises() as $obstacleexercise) {
                $this->table->add_row('<a href="'.base_url('index.php/admin/obstaclesexercises/'.$obstacleexercise->id).'">Delete</a>', $this->keyword_model->get_keyword($obstacleexercise->obstacleid)[0]->keyword, $this->keyword_model->get_keyword($obstacleexercise->exerciseid)[0]->keyword);
            }

            $data->obstacles = $this->utilities->builddropdownarraykeyword($this->keyword_model->get_obstacles());
            $data->exercises = $this->utilities->builddropdownarraykeyword($this->keyword_model->get_exercises());
            
            $this->load->view('header', $data);
            $this->load->view('admin/obstaclesexercises', $data);
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }

    public function obstaclesraces($id = NULL) {
        $data = new stdClass();
        $data->message = '';
       
        if ($_SESSION['is_admin']) {
            if ($this->input->post('obstacle') > 0 && $this->input->post('race') > 0) {
                if ($this->obstaclesraces_model->create_obstaclerace($this->input->post('obstacle'), $this->input->post('race'))) {
                    $data->message = 'Obstacle/Race created';
                }
                else {
                    $data->message = 'There was a problem creating the obstacle/race';
                }    
            }
            else if ($id>0) {
                if ($this->obstaclesraces_model->delete_obstaclerace($id)) {
                    $data->message = 'Obstacle/Race deleted';
                }
                else {
                    $data->message = 'There was a problem deleting the obstacle/race';
                }
            }
            
            $this->table->set_heading('ID', 'ObstacleID', 'ExerciseID');
            foreach($this->obstaclesraces_model->get_obstaclesraces() as $obstaclerace) {
                $this->table->add_row('<a href="'.base_url('index.php/admin/obstaclesraces/'.$obstaclerace->id).'">Delete</a>', $this->keyword_model->get_keyword($obstaclerace->obstacleid)[0]->keyword, $this->race_model->get_race($obstaclerace->raceid)[0]->race);
            }

            $races = array();
            foreach ($this->race_model->get_races() as $race)
            {
                $races[$race->id] = $race->race;
            }
        
            $data->obstacles = $this->utilities->builddropdownarraykeyword($this->keyword_model->get_obstacles());
            $data->races = $races;
            
            $this->load->view('header', $data);
            $this->load->view('admin/obstaclesraces', $data);
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }

    public function garmin($start = 1) {
        $data = new stdClass();
        $data->message = '';

        if ($_SESSION['is_admin']) {
            $data->start = $start;

            $json_data = json_decode(file_get_contents('http://connect.garmin.com/proxy/activitylist-service/activities/mattellenburg?start=1&limit=10000'), true);
            $activitylist = $json_data['activityList'];
            $data->activities = sizeof($activitylist);
            $this->table->set_heading('Name', 'Date', 'Activity Type', 'Event Type', 'Distance', 'Duration', 'Calories', 'Calories/Minute', 'Avg HR', 'Max HR', 'Description');

            for($i=($start*10)-10; $i<($start*10); $i++) {
                $this->table->add_row('<a href="https://connect.garmin.com/modern/activity/'.$activitylist[$i]['activityId'].'" target="_blank">'.$activitylist[$i]['activityName']."</a>", date('m-d-Y', strtotime($activitylist[$i]['startTimeLocal'])), $activitylist[$i]['activityType']['typeKey'], $activitylist[$i]['eventType']['typeKey'], number_format(floatval($activitylist[$i]['distance'])*0.00062137, 2), gmdate("H:i:s", $activitylist[$i]['duration']), number_format($activitylist[$i]['calories'], 0), number_format((float) $activitylist[$i]['calories']/(float) ($activitylist[$i]['duration']/60), 0), $activitylist[$i]['averageHR'], $activitylist[$i]['maxHR'], $activitylist[$i]['description']);
            }

            $this->load->view('header', $data);
            $this->load->view('admin/garmin', $data);
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }
}
