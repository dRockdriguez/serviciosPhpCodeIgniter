<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Login extends REST_Controller {

    public function __construct(){
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");
        
        parent::__construct();
        $this->load->database();
    }

    public function index_post(){
        $data = $this->post();

        if (!isset($data['correo']) OR !isset($data['pass'])) {
            $respuesta = array('error' => TRUE, 'mensaje' => 'Login incorrecto');
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $correo = $data['correo'];
        $pass = $data['pass'];
        $condiciones = array('correo' => $correo,
                        'contrasena' => $pass);
        $query = $this->db->get_where('login', $condiciones);
        $usuario = $query->row();

        if (!isset($usuario)){
            $respuesta = array('error' => TRUE, 'mensaje' => 'Login incorrecto');
            $this->response($respuesta);
            return;
        }

        // Generamos "token"
        $token = bin2hex(openssl_random_pseudo_bytes(20));
        $token = hash('ripemd160', $data['correo']);

        $this->db->reset_query();
        $actualizar_token = array('token' => $token);
        $this->response($usuario->id);
        $this->db->where('id', $usuario->id);
        $hecho = $this->db->update('login', $actualizar_token);
        $respuesta = array(
            'error' => FALSE,
            'token' => $token,
            'id' => $usuario->id
        );
        $this->response($respuesta);


    }

}