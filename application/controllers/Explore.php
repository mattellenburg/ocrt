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
        $this->load->library('googlemaps');
    }

    private function add_pending_point($title, $description, $latitude, $longitude, $icon) {
        if ($this->point_pending_model->create_point($title, $description, $latitude, $longitude, $icon)) {				
            return 'Your location has been submitted for review.';
        } 
        else {
            return 'There was a problem creating your location.';
        }
    }
    
    private function rate_location($pointid, $rating) { 
        if ($this->pointsratings_model->get_rating($pointid)->num_rows() > 0) {
            if ($this->pointsratings_model->update_rating($pointid, $rating)) {
                return 'Your rating has been updated.';				
            }
            else {
                return 'There was a problem updating your rating.';
            }
        }
        else {
            if ($this->pointsratings_model->create_rating($pointid, $rating)) {				
                return 'Your rating has been recorded.';					
            } 
            else {
                return 'There was a problem saving your rating.';
            }
        }
    }
    
    private function update_keywords($data, $pointid, $keywords) {
        if (sizeof($keywords) > 0) {
            if ($this->pointskeywords_model->update_pointkeyword($pointid)) {				
                foreach ($keywords as $keywordid):
                    if ($this->pointskeywords_model->get_pointkeyword($pointid, $keywordid)->num_rows() > 0) {
                        if ($this->pointskeywords_model->update_pointkeyword($pointid, $keywordid, 0)) {				
                            return 'Your keywords have been recorded.';					
                        } 
                        else {
                            return 'There was a problem saving your keywords.';
                        }
                    }
                    else {
                        if ($this->pointskeywords_model->create_pointkeyword($pointid, $keywordid)) {				
                            return 'Your keywords have been recorded.';					
                        } 
                        else {
                            return 'There was a problem saving your keywords.';
                        }
                    }
                endforeach;
            }
        }
        else {
            if ($this->pointskeywords_model->update_pointkeyword($pointid)) {				
                return 'Your keywords have been deleted.';					
            } 
            else {
                return 'There was a problem deleting your keywords.';
            }
        }        
    }
    
    private function get_filters($filters) {
        $filter = '';
        if ($filters->rating > 0) {
            $filter = $filter.'<li>Average Rating: '.strval($filters->rating).'+</li>';
        }
        
        if ($filters->search > '') {
            $filter = $filter.'<li>Search term: '.$filter->search.'</li>';
        }
        
        if ($filters->keywords > '') {
            $filter = $filter.'<li>Keyword(s): <ul>';
            foreach ($filters->keywords as $filterkeywordid) {
                $filter = $filter.'<li>'.$this->keyword_model->get_keyword($filterkeywordid)[0]->keyword.'</li>';
            }
            $filter = $filter.'</ul></li>';
        }
        
        if (isset($filters->userid)) {
            $filter = $filter.'<li>My submissions</li>';
        }
        
        if ($filter !== '') {
            $filter = 'Current filters: <ul class="selectedfilters">'.$filter.'</ul>';
        }
        
        return $filter;
    }
    
    public function index($mapview = 1, $zoom = 13, $latitude = NULL, $longitude = NULL, $pointid = NULL, $rating = NULL) {
        $headerdata = new stdClass();
        $headerdata->pageinformation = 'Use the map to view training locations.  Registered users may submit new locations for review and edit existing locations.  Use the filters to narrow your location search.  Only locations within 3 miles of the map center are displayed.';        

        $data = new stdClass();
        
        $filters = new stdClass();
        $filters->rating = $this->input->get('filterrating', TRUE);
        $filters->search = $this->input->get('filtersearch', TRUE);
        $filters->keywords = $this->input->get('filterkeywords', TRUE);
        if ($this->input->get('mysubmissions', TRUE) === 'on') { $filters->userid = $_SESSION['user_id']; }
        
        if ($this->input->get('title', TRUE) <> '' && $this->input->get('description', TRUE) <> '') {
            $data->message = $this->add_pending_point($this->input->get('title', TRUE), $this->input->get('description', TRUE), $this->input->get('latitude', TRUE), $this->input->get('longitude', TRUE), $this->input->get('icon', TRUE));
        }
        else if ($rating > 0) {
            $data->message = $this->rate_location($pointid, $rating);
        }
        else if ($this->input->post('submit') == 'Update Keywords') {
            $data->message = $this->update_keywords($pointid, $this->input->get('locationkeywords', TRUE));
        }

        $data->mapview = $mapview;
        $data->zoom = $zoom;
        $data->latitude = $latitude;
        $data->longitude = $longitude;
        $data->pointid = $pointid;
        $data->keywords = $this->keyword_model->get_keywords();
        $data->points_pending = $this->point_pending_model->get_points();
        $data->points = $this->point_model->get_points($latitude, $longitude, $filters);
        $data->filters = $this->get_filters($filters);
        
        $this->load->view('header', $headerdata);
        $this->load->view('explore', $data);
        $this->load->view('footer');	
    }
}
