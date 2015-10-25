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
        $this->load->model('pointskeywords_model');		
        $this->load->model('keyword_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index($pointid = NULL) {
        $data = new stdClass();

        if ($this->input->post('rating') > 0) {
            $query = $this->pointsratings_model->get_rating($pointid);

            if ($query->num_rows() > 0) {
                if ($this->pointsratings_model->update_rating($pointid, $this->input->post('rating'))) {
                    $data->message = 'Your rating has been updated.';				
                }
                else {
                    $data->message = 'There was a problem updating your rating.';
                }
            }
            else {
                if ($this->pointsratings_model->create_rating($pointid, $this->input->post('rating'))) {				
                    $data->message = 'Your rating has been recorded.';					
                } 
                else {
                    $data->message = 'There was a problem saving your rating.';
                }
            }
        }
        
        if(sizeof($this->input->post('keywords'))) {
            if ($this->pointskeywords_model->update_pointkeyword($pointid)) {				
                foreach ($this->input->post('keywords') as $keywordid):
                    $pointkeyword = $this->pointskeywords_model->get_pointkeyword($pointid, $keywordid);
                    if ($pointkeyword->num_rows() > 0) {
                        if ($this->pointskeywords_model->update_pointkeyword($pointid, $keywordid, 0)) {				
                            $data->message = 'Your keywords have been recorded.';					
                        } 
                        else {
                            $data->message = 'There was a problem saving your keywords.';
                        }
                    }
                    else {
                        if ($this->pointskeywords_model->create_pointkeyword($pointid, $keywordid)) {				
                            $data->message = 'Your keywords have been recorded.';					
                        } 
                        else {
                            $data->message = 'There was a problem saving your keywords.';
                        }
                    }
                endforeach;
            }
            else {
                $data->message = 'There was a problem saving your keywords.';
            }
        }
        else {
            if ($this->pointskeywords_model->update_pointkeyword($pointid)) {				
                $data->message = 'Your keywords have been deleted.';					
            } 
            else {
                $data->message = 'There was a problem deleting your keywords.';
            }
        }
        
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
