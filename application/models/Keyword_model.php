<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keyword_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_keyword($keyword, $exercise = NULL, $obstacle = NULL) {		
        $data = array(
            'keyword' => $keyword,
            'exercise' => $exercise,
            'obstacle' => $obstacle
        );

        return $this->db->insert('keywords', $data);
    }

    public function update_keyword($id, $keyword, $exercise = NULL, $obstacle = NULL) {
        $data = array(
            'keyword' => $keyword,
            'exercise' => $exercise,
            'obstacle' => $obstacle
        );

        $where = array(
            'id' => $id
        );

        return $this->db->update('keywords', $data, $where);
    }

    public function get_keyword($id) {
        return $this->db->get_where('keywords', array('id' => $id))->result();      
    }

    public function get_keyword_by_keyword($keyword) {
        return $this->db->get_where('keywords', array('keyword' => urldecode($keyword)))->result();      
    }

    public function get_keywords() {
        $this->db->order_by('keyword');
        return $this->db->get('keywords')->result();      
    }
    
    public function get_exercises() {
        $this->db->order_by('keyword');
        return $this->db->get_where('keywords', array('exercise' => 1))->result();      
    }
    
    public function get_obstacles() {
        $this->db->order_by('keyword');
        return $this->db->get_where('keywords', array('obstacle' => 1))->result();      
    }
}
