<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Pedidos extends REST_Controller {

    public function __construct(){
        header("Access-Control-Allow-Methods: GET, POST");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
        
        parent::__construct();
        $this->load->database();
    }

    public function realizar_orden_post($token = "0", $id = "0"){
        $data = $this->post();

        if ($token == "0" || $id == "0") {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Token o usuario inválido'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        
        if (!isset($data['items']) || strlen($data['items']) == 0) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Faltan los items'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $condiciones = array(
            'id' => $id,
            'token' => $token
        );
        $this->db->where($condiciones);
        $query = $this->db->get('login', $condiciones);
        $existe = $query->row();

        if(!$existe){
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Usuario o token incorrectos'
            );
            $this->response($respuesta);
            return;
        }
    }
}