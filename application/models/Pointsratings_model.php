<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pointsratings_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_rating($pointid, $rating) {		
        $data = array(
            'pointid' => $pointid,
            'rating' => $rating,
            'userid' => $_SESSION['user_id'],
            'ratingdate' => date('Y-m-j H:i:s')
        );

        return $this->db->insert('pointsratings', $data);
    }	

    public function get_rating($pointid) {
        return $this->db->get_where('pointsratings', array('userid' => $_SESSION['user_id'], 'pointid' => $pointid));      
    }

    public function update_rating($pointid, $rating) {
        $data = array('rating' => $rating);

        $this->db->where('pointid', $pointid);
        $this->db->where('userid', $_SESSION['user_id']);
        $this->db->update('pointsratings', $data); 
        
        return 1;
    }
}
