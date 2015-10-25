<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pointskeywords_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_pointkeyword($pointid, $keywordid) {		
        $data = array(
            'pointid' => $pointid,
            'keywordid' => $keywordid,
            'deleteflag' => 0,
            'userid' => $_SESSION['user_id'],
            'updatedate' => date('Y-m-j H:i:s')
        );

        return $this->db->insert('pointskeywords', $data);
    }
    
    public function update_pointkeyword($pointid, $keywordid = NULL, $delete = 1) {
        $data = array(
            'deleteflag' => $delete,
            'updatedate' => date('Y-m-j H:i:s')
        );

        if ($keywordid == NULL) {
            $where = array(
                'pointid' => $pointid,
                'userid' => $_SESSION['user_id']
            );
        }
        else {
            $where = array(
                'pointid' => $pointid,
                'keywordid' => $keywordid,
                'userid' => $_SESSION['user_id']
            );
        }

        return $this->db->update('pointskeywords', $data, $where);
    }

    public function get_pointkeywords($pointid) {
        return $this->db->get_where('pointskeywords', array('pointid' => $pointid));      
    }

    public function get_pointkeyword($pointid, $keywordid) {
        return $this->db->get_where('pointskeywords', array('pointid' => $pointid, 'keywordid' => $keywordid));      
    }
}
