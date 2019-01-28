<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Prueba extends REST_Controller {

    public function __construct(){
        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
        
        parent::__construct();
        $this->load->database();
    }
    public function index(){
        echo "Hola Mundo!!";
    }

    public function obtener_array_get($index = 0){
        if ($index > 2) {
            $resp = array('error' => TRUE, 'mensaje' => 'No existe el elemento');
            $this->response($resp, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $array = array('Hola', 'Que', 'Tal');
            $resp = array('error' => FALSE, 'mensaje'=> $array[$index]);
            $this->response($resp);
        }

    }

    public function obtener_producto_get($codigo){
        $query = $this->db->query("SELECT * FROM productos WHERE codigo='".$codigo."'");

        $this->response($query->result());
    }
}