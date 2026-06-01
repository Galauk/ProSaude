<?php

class UsuariosTest extends PHPUnit_Framework_TestCase {

    private $object;

    public function setUp() {
        $this->object = new Application_Model_Usuarios();
    }
    
    public function tearDown() {
    }

    public function testPodeCriarUsuario() {
        //$this->assertEquals(true, $this->object->verificaLoginExistente(false));        
    }
}