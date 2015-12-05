<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities {
    public function build_filter_class($rating, $search, $keywords, $mysubmissions) {        
        $filters = new stdClass();
        $filters->rating = $rating;
        $filters->search = $search;
        $filters->keywords = $keywords;

        if ($mysubmissions === 'on') { $filters->userid = $_SESSION['user_id']; }   

        return $filters;
    }

    public function get_filter_text($filters) {
        $CI =& get_instance();
        $CI->load->model('keyword_model');

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
                    $filter = $filter.'<li>'.$CI->keyword_model->get_keyword($filterkeywordid)[0]->keyword.'</li>';
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

    public function build_where ($filters, $mysubmissions) {
        $where = new stdClass();

        $where->where1 = ' where (p.title like '."'%".$filters->search."%'".' or p.description like '."'%".$filters->search."%'".')';
        $where->where2 = ' where p.distance <= 3';

        if ($filters->rating > 0) {
            $where->where1 .= ' and pr.avgrating >= '.$filters->rating;
        }
        
        if ($mysubmissions == 'on') {

            $where->where1 .= ' and p.createdbyid = '.(string)$filters->userid;
        }

        if (isset($filters->keywords)) {
            if (sizeof($filters->keywords) > 0) {
                $where->where2 .= ' and (';
                foreach ($filters->keywords as $keywordid) {
                    $where->where2 .= 'p.keywords like \'%'.$this->keyword_model->get_keyword($keywordid)[0]->keyword.'%\' OR ';
                }
                $where->where2 = substr($where->where2, 0, strlen($where->where2)-3).')';
            }
        }
        
        return $where;
    }
    
    public function calculate_distance ($latitude, $longitude) {
        return '3959*acos(cos(radians('.$latitude.'))*cos(radians(latitude))*cos(radians(longitude)-radians('.$longitude.'))+sin(radians('.$latitude.'))*sin(radians(latitude)))';
    }

    public function builddropdownarrayroute($source) {
        $array = array();
        if (sizeof($source) > 0) {
            foreach ($source as $item) {
                $array[$item['id']] = $item['routename'];
            }            
        }
        return $array;
    }

    public function builddropdownarraykeyword($source) {
        $array = array();
        foreach ($source as $item) {
            $array[$item->id] = $item->keyword;
        }
        return $array;
    }
}
?>

