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

    public function index($zoom = NULL, $latitude = NULL, $longitude = NULL, $pointid = NULL) {
        $data = new stdClass();

        $where = ' where (p.title like '."'%".$this->input->post('search')."%'".' or p.description like '."'%".$this->input->post('search')."%'".')';        echo $this->input->post('filterkeywords').'<BR>';
        $filter = '';
        if ($this->input->post('filterrating') > 0) {
            $where = $where.' and pr.avgrating >= '.strval($this->input->post('filterrating'));
            $filter = $filter.'Average Rating: '.strval($this->input->post('filterrating')).'+<br>';
        }
        if ($this->input->post('mysubmissions') == 'on') {
            $where = $where.' and p.createdbyid = '.$_SESSION['user_id'];
            $filter = $filter.'My submissions<br>';
        }
        if ($this->input->post('search') > '') {
            $filter = $filter.'Search term: '.$this->input->post('search');
        }
        
        if ($filter !== '') {
            $data->filter = '<h3>Current selections</h3><p><i>'.$filter.'</i></p>';
        }
        else {
            $data->filter = '';
        }

        $data->zoom = $zoom;
        $data->latitude = $latitude;
        $data->longitude = $longitude;
        
        if ($this->input->post('locationrating') > 0) {
            $query = $this->pointsratings_model->get_rating($pointid);

            if ($query->num_rows() > 0) {
                if ($this->pointsratings_model->update_rating($pointid, $this->input->post('locationrating'))) {
                    $data->message = 'Your rating has been updated.';				
                }
                else {
                    $data->message = 'There was a problem updating your rating.';
                }
            }
            else {
                if ($this->pointsratings_model->create_rating($pointid, $this->input->post('locationrating'))) {				
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

        $data->points = $this->point_model->get_points($where, $latitude, $longitude);
        $data->points_pending = $this->point_pending_model->get_points();
        $data->keywords = $this->keyword_model->get_keywords();

        $this->load->view('header', $data);
        $this->load->view('explore', $data);
        $this->load->view('footer');	
    }
}
