<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point_model extends CI_Model {
    public function __construct() {		
        parent::__construct();
        $this->load->database();		
    }

    public function create_point($title, $description, $latitude, $longitude, $icon) {		
        $data = array(
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'createdbyid' => $_SESSION['user_id'],
            'createdate' => date('Y-m-j H:i:s'),
            'lastmodifiedbyid' => $_SESSION['user_id'],
            'lastmodifieddate' => date('Y-m-j H:i:s')
        );

        return $this->db->insert('points', $data);
    }

    public function get_points($where = NULL, $latitude = NULL, $longitude = NULL, $keyword = NULL) {
        if (isset($_SESSION['user_id'])) {
            if ($latitude !== NULL && $longitude !== NULL) {
                return $this->db->query('select * from (select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, (3959*acos(cos(radians('.$latitude.'))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians('.$longitude.'))+sin(radians('.$latitude.'))*sin(radians(p.latitude)))) as distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$_SESSION['user_id'].') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where.' and (3959*acos(cos(radians('.$latitude.'))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians('.$longitude.'))+sin(radians('.$latitude.'))*sin(radians(p.latitude)))) <= 3 group by p.id) as p'.$keyword)->result_array();       
            }
            else {
                return $this->db->query('select * from (select p.*, userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, NULL as distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select pointid, rating as userrating from pointsratings where userid='.$_SESSION['user_id'].') as pru on p.id=pru.pointid  left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where.' group by p.id) as p'.$keyword)->result_array();       
            }
        }
        else {
            if ($latitude !== NULL && $longitude !== NULL) {
                return $this->db->query('select * from (select p.*, NULL as userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, (3959*acos(cos(radians('.$latitude.'))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians('.$longitude.'))+sin(radians('.$latitude.'))*sin(radians(p.latitude)))) AS distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id '.$where.' and (3959*acos(cos(radians('.$latitude.'))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians('.$longitude.'))+sin(radians('.$latitude.'))*sin(radians(p.latitude)))) <= 3 group by p.id) as p'.$keyword)->result_array();
            }
            else
            {
                return $this->db->query('select * from (select p.*, NULL as userrating, avgrating, GROUP_CONCAT(k.keyword) as keywords, NULL AS distance from points as p left join (select pointid, avg(rating) as avgrating from pointsratings group by pointid) as pr on p.id=pr.pointid left join (select * from pointskeywords where deleteflag=0) as pk on p.id=pk.pointid left join keywords k on pk.keywordid=k.id group by p.id) as p'.$keyword)->result_array();
            }
        }
    }   
}
