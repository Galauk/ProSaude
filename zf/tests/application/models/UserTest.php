<?php

class UserTest extends PHPUnit_Framework_TestCase {

    private $user;
    private $users_data;

    //a cada teste passa aqui
    public function setUp() {
        $this->user = new Application_Model_User();
        for ($i = 0; $i < 10; $i++) {
            $this->users_data[$i] = array(
                "name" => "Victor", 
                "password" => "123", 
                "email" => "teste_"
            );
        }
    }
    
    public function tearDown() {
        $dbTable = new Application_Model_User();
        //$db = $dbTable->getAdapter();
       // $db->query("truncate table usuarios");
    }

    public function testPodeCriarUsuario() {
        $result = $this->user->create($this->users_data[0]);
        $this->assertEquals(1, $result);
    }

    

    //put your code here
}
