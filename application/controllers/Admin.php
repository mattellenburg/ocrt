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
        $this->load->library('table');
        $this->load->helper('form');
        $this->load->library('form_validation');
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
    
    public function approvepoints($id = NULL) {
        $data = new stdClass();
        $data->message = '';

        if ($_SESSION['is_admin']) {
            if ($id > 0) {
                $pending_point = new stdClass();
                $pending_point = $this->point_pending_model->get_point($id);

                if ($this->point_model->create_point($pending_point->title, $pending_point->description, $pending_point->latitude, $pending_point->longitude, $pending_point->icon)) {					
                    $this->point_pending_model->delete_point($id);				
                    $data->message = 'Point has been approved.';
                } 
                else {				
                    $data->message = 'There was a problem approving the point.';
                }
            }

            $data->points_pending = $this->point_pending_model->get_points();

            $this->load->view('header', $data);
            $this->load->view('admin/approvepoints');
            $this->load->view('footer');
        }
        else {
            redirect('/');
        }
    }
}
