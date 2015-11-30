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
    
    public function index($mapview = 1, $zoom = 13, $latitude = NULL, $longitude = NULL, $pointid = NULL, $querykeyword = NULL) {
        $headerdata = new stdClass();
        $headerdata->pageinformation = 'Use the map to view training locations.  Registered users may submit new locations for review and edit existing locations.  Use the filters to narrow your location search.  Only locations within 3 miles of the map center are displayed.';        

        $data = new stdClass();

        $data->querykeyword = '';
        if ($this->input->get('keyword', TRUE) > '') {
            $data->querykeyword = $this->input->get('keyword', TRUE);
        }

        $filters = new stdClass();
        $filters->rating = $this->input->post('filterrating', TRUE);
        $filters->search = $this->input->post('filtersearch', TRUE);

        if ($querykeyword !== NULL) {
            $filters->keywords = array($this->keyword_model->get_keyword_by_keyword($querykeyword)[0]->id);
        }
        if ($this->input->post('filterkeywords', TRUE) > '') {
            $filters->keywords = $this->input->post('filterkeywords', TRUE);
        }

        if ($this->input->post('mysubmissions', TRUE) === 'on') { $filters->userid = $_SESSION['user_id']; }
        
        if ($this->input->post('deletelocation', TRUE) == 'Delete Location') {
            $headerdata->message = $this->delete_pending_point($this->input->post('pendingpointid', TRUE));
        }
        else if ($this->input->post('requestdeletion', TRUE) == 'Request Location Deletion') {
            $headerdata->message = $this->add_pending_point($this->input->post('title', TRUE), $this->input->post('description', TRUE), $this->input->post('latitude', TRUE), $this->input->post('longitude', TRUE), $this->input->post('icon', TRUE), $this->input->post('pointid', TRUE), $this->input->post('pendingpointid', TRUE), 1);
        }
        else if ($this->input->post('submitlocation', TRUE) == 'Submit Location Information' && $this->input->post('title', TRUE) <> '' && $this->input->post('description', TRUE) <> '') {
            $headerdata->message = $this->add_pending_point($this->input->post('title', TRUE), $this->input->post('description', TRUE), $this->input->post('latitude', TRUE), $this->input->post('longitude', TRUE), $this->input->post('icon', TRUE), $this->input->post('pointid', TRUE), $this->input->post('pendingpointid', TRUE));
        }
        else if ($this->input->post('ratingkeywordssubmit', TRUE) == 'Submit Rating and Keywords') {
            $headerdata->message = $this->rate_location($pointid, $this->input->post('locationrating', TRUE));
            $headerdata->message = $this->update_keywords($pointid, $this->input->post('locationkeywords', TRUE));
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

    private function add_pending_point($title, $description, $latitude, $longitude, $icon, $pointid, $pendingpointid, $delete = NULL) {
        if (sizeof($this->point_pending_model->get_point($pendingpointid)) > 0) {
            if ($this->point_pending_model->update_point($pendingpointid, $title, $description, $latitude, $longitude, $icon, $pointid)) {	
                return 'Your information has been updated and submitted for review';
            }
            else {
                return 'There was a problem updating your information';
            }
        }
        else {
            if ($this->point_pending_model->create_point($title, $description, $latitude, $longitude, $icon, $pointid, $delete)) {				
                return 'Your information has been submitted for review.';
            } 
            else {
                return 'There was a problem submitting your information.';
            }
        }
    }

    private function delete_pending_point($pendingpointid) {
        if ($this->point_pending_model->delete_point($pendingpointid)) {	
            return 'Your location has been deleted';
        }
        else {
            return 'There was a problem deleting your location';
        }
    }
    
    private function rate_location($pointid, $rating) { 
        if ($this->pointsratings_model->get_rating($pointid)->num_rows() > 0 && $rating > 0) {
            if ($this->pointsratings_model->update_rating($pointid, $rating)) {
                return 'Your rating has been updated.';				
            }
            else {
                return 'There was a problem updating your rating.';
            }
        }
        else if ($rating > 0) {
            if ($this->pointsratings_model->create_rating($pointid, $rating)) {				
                return 'Your rating has been recorded.';					
            } 
            else {
                return 'There was a problem saving your rating.';
            }
        }
    }
    
    private function update_keywords($pointid, $keywords) {
        if (sizeof($keywords) > 0) {
            $this->pointskeywords_model->delete_pointkeywords($pointid);
            foreach ($keywords as $keywordid):
                if ($this->pointskeywords_model->get_pointkeyword($pointid, $keywordid)->num_rows() == 0) {
                    $this->pointskeywords_model->create_pointkeyword($pointid, $keywordid);
                }
            endforeach;
            return 'Your keywords have been recorded.';					
        }
        else {
            if ($this->pointskeywords_model->delete_pointkeywords($pointid)) {				
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
            $filter = $filter.'<li>Search term: '.$filters->search.'</li>';
        }
        
        if (isset($filters->keywords)) {
            if ($filters->keywords > '') {
                $filter = $filter.'<li>Keyword(s): <ul>';
                foreach ($filters->keywords as $filterkeywordid) {
                    $filter = $filter.'<li>'.$this->keyword_model->get_keyword($filterkeywordid)[0]->keyword.'</li>';
                }
                $filter = $filter.'</ul></li>';
            }
        }
        
        if (isset($filters->userid)) {
            $filter = $filter.'<li>My submissions</li>';
        }
        
        if ($filter !== '') {
            $filter = 'Current filters: <ul class="selectedfilters">'.$filter.'</ul>';
        }
        
        return $filter;
    }
}