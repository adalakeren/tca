<?php

class Welcome extends Controller {

    function Welcome() {
        parent::Controller();
        //$this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('session');
    }

    function index() {
        $this->load->view('template');
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */