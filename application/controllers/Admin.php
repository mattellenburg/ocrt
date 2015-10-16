<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url'));
		$this->load->model('point_model');
		$this->load->model('point_pending_model');
		
	}

	public function index() {

		$this->load->view('header');
		$this->load->view('admin/index');
		$this->load->view('footer');
	
	}

	public function approvepoints($id = NULL) {

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
		
		$this->load->view('header');
		$this->load->view('admin/approvepoints', $data);
		$this->load->view('footer');
	
	}
}
