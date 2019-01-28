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

        // Usuario y token son correctos
        $this->db->reset_query();
        $insertar = array(
            'usuario_id' => $id
        );
        $this->db->insert('ordenes', $insertar);
        $orden_id = $this->db->insert_id();

        //Crear detalle de orden
        $this->db->reset_query();
        $items = explode(',', $data['items']);

        foreach($items as $producto_id) {
            $data_insertar = array(
                'producto_id' => $producto_id,
                'orden_id' => $orden_id
            );
            $this->db->insert('ordenes_detalle', $data_insertar);
        }

        $respuesta = array(
            'error' => FALSE,
            'orden_id' => $orden_id
        );

        $this->response($respuesta);
    }

    public function obtener_pedidos_get($token = "0", $id = "0"){
        $data = $this->post();

        if ($token == "0" || $id == "0") {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Token o usuario inválido'
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

        $query = $this->db->query("SELECT * FROM ordenes WHERE usuario_id='$id'");
        $ordenes = array();
        foreach($query->result() as $row){
            $query_detalle = $this->db->query("SELECT a.orden_id, b.* FROM ordenes_detalle a inner join productos b on a.producto_id = b.codigo WHERE orden_id = $row->id");
            $orden = array(
                'id' => $row->id,
                'creado_en' => $row->creado_en,
                'detalle' => $query_detalle->result()
            );

            array_push($ordenes, $orden);
        }
        $respuesta = array(
            'error' => FALSE,
            'ordenes' => $ordenes
        );
        $this->response(
            $respuesta
        );

    }
}