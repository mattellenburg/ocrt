<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pointseywords_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_pointkeyword($pointid, $keywordid) {		
        $data = array(
            'pointid' => $pointid,
            'keywordid' => $keywordid
        );

        return $this->db->insert('pointskeywords', $data);
    }

    public function get_pointkeywords($pointid) {
        return $this->db->get_where('pointskeywordss', array('pointid' => $pointid));      
    }
}
