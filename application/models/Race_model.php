<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Race_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_race($race, $date, $location, $description) {		
        $data = array(
            'race' => $race,
            'date' => $date,
            'location' => $location,
            'description' => $description
        );

        return $this->db->insert('races', $data);
    }

    public function get_races() {
        return $this->db->get('races')->result();      
    }   
}
