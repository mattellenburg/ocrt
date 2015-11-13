<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('point_model');
        $this->load->model('point_pending_model');
        $this->load->library('table');
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

    public function garmin($start = 1) {
        $data = new stdClass();
        $data->message = '';
        $data->start = $start;

        $json_data = json_decode(file_get_contents('http://connect.garmin.com/proxy/activitylist-service/activities/mattellenburg?start=1&limit=10000'), true);
        $activitylist = $json_data['activityList'];
        $data->activities = sizeof($activitylist);
        $this->table->set_heading('ID', 'Name', 'Description', 'Date', 'Activity Type', 'Event Type', 'Distance', 'Duration', 'Calories', 'Avg HR', 'Max HR');

        for($i=($start*10)-10; $i<($start*10); $i++) {
            $this->table->add_row($activitylist[$i]['activityId'], $activitylist[$i]['activityName'], $activitylist[$i]['description'], date('m-d-Y', strtotime($activitylist[$i]['startTimeLocal'])), $activitylist[$i]['activityType']['typeKey'], $activitylist[$i]['eventType']['typeKey'], number_format(floatval($activitylist[$i]['distance'])*0.00062137, 2), gmdate("H:i:s", $activitylist[$i]['duration']), number_format($activitylist[$i]['calories'], 0), $activitylist[$i]['averageHR'], $activitylist[$i]['maxHR']);
        }

        if ($_SESSION['is_admin']) {
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
            $data = new stdClass();

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
