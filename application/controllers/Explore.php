<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Explore extends CI_Controller {
    public function __construct() {		
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('point_model');
        $this->load->model('point_pending_model');
        $this->load->model('pointsratings_model');		
        $this->load->model('keyword_model');
    }

    public function index($pointid = NULL) {
        $data = new stdClass();

        $title = $this->input->get('title', TRUE);
        $description = $this->input->get('description', TRUE);
        $icon = $this->input->get('icon', TRUE);
        $latitude = $this->input->get('latitude', TRUE);
        $longitude = $this->input->get('longitude', TRUE);
        $rating = $this->input->get('rating', TRUE);

        if ($title <> '' && $description <> '') {
            if ($this->point_pending_model->create_point($title, $description, $latitude, $longitude, $icon)) {				
                $data->message = 'Your location has been submitted for review.';
            } 
            else {
                $data->message = 'There was a problem creating your location.';
            }
        }
        else if ($rating > 0) {
            $query = $this->pointsratings_model->get_rating($pointid);

            if ($query->num_rows() > 0) {
                if ($this->pointsratings_model->update_rating($pointid, $rating)) {
                    $data->message = 'Your rating has been updated.';				
                }
                else {
                    $data->message = 'There was a problem updating your rating.';
                }
            }
            else {
                if ($this->pointsrating_model->create_rating($pointid, $rating)) {				
                    $data->message = 'Your rating has been recorded.';					
                } 
                else {
                    $data->message = 'There was a problem saving your rating.';
                }
            }
        }
        else {
            $data->message = '';
        }

        $data->points = $this->point_model->get_points();
        $data->points_pending = $this->point_pending_model->get_points();
        $data->keywords = $this->keyword_model->get_keywords();
        
        $this->load->view('header');
        $this->load->view('explore', $data);
        $this->load->view('footer');			
    }
}
