<?php

require_once 'HTTP/Client.php';

class Bugzilla_Bug {
    public $product;
    public $version;
    public $component;
    public $rep_platform;
    public $op_sys;
    public $priority;
    public $bug_severity;
    public $bug_status;
    public $assigned_to;
    public $cc;
    public $bug_file_loc;
    public $short_desc;
    public $comment;
    public $commentprivacy;
    public $dependson;
    public $blocked;
    public $form_name;
    
    public function __construct($field_array = NULL) {
        if (is_array($field_array)) {
            foreach ($field_array as $k => $f) {
                $this->{$k} = $f;
            }
        }
    }
    
    public function __toString() {
        $vars = get_object_vars($this);
        $string = array();
        foreach ($vars as $k => $v) {
            $string[] = "$k=" . urlencode($v);
        }
        return $string = implode('&', $string);
    }
    
    public function getString() {
        return $this->__toString();
    }
    
}

class Bugzilla {
    private $server;
    private $email;
    private $password;
    
    private $_http_client;
    
    public function __construct($server, $email, $password) {
        $this->server   = $server;
        $this->email    = $email;
        $this->password = $password;
        
        $this->_http_client = new HTTP_Client();
        $this->_login();
    }
    
    private function _login() {
        $url = $this->server . '/index.cgi';
        $preEncoded = TRUE;
        $params = array();
        $params['Bugzilla_login']    = urlencode($this->email);
        $params['Bugzilla_password'] = urlencode($this->password);
        $params['GoAheadAndLogIn'] = 'Login';
        $data = array();
        foreach ($params as $k => $v) {
            $data[] = "$k=$v";
        }
        $data = implode('&', $data);
        $responde_code = $this->_http_client->post($url, $data, $preEncoded);
        // handle response code
    }
    
    public function postBug($Bugzilla_Bug) {
        $url = $this->server . '/post_bug.cgi';
        $preEncoded = TRUE;
        $data = $Bugzilla_Bug->getString();
        return $response_code = $this->_http_client->post($url, $data, $preEncoded);
        // handle response code
    }

}

/*
// set each property separately or pass a named array to the constructor
$bug = new Bugzilla_Bug();
$bug->product        = 'TestProduct';
$bug->version        = 'other';
$bug->component      = 'TestComponent';
$bug->rep_platform   = 'All';
$bug->op_sys         = 'All';
$bug->priority       = 'P1';
$bug->bug_severity   = 'minor';
$bug->bug_status     = 'NEW';
$bug->assigned_to    = 'jeff@newnewmedia.com';
$bug->cc             = '';
$bug->bug_file_loc   = 'http://google.com';
$bug->short_desc     = 'summary';
$bug->comment        = 'description';
$bug->commentprivacy = 0;
$bug->form_name      = 'enter_bug';
$bz = new Bugzilla('http://some_bugzilla_url', 'jeff@newnewmedia.com', 'password');
$bz->postBug($bug);
exit;
*/

/*
$array = array(
    'product' => 'TestProduct',
    'version' => 'other',
    'component' => 'TestComponent'
);
$bug = new Bugzilla_Bug($array);
print $bug->getString();
exit;
*/

?>