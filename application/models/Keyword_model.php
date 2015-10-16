<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keyword_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_keyword($keyword) {		
        $data = array(
            'keyword' => $keyword
        );

        return $this->db->insert('keywords', $data);
    }

    public function get_keywords() {
        return $this->db->get('keywords')->result();      
    }
}
