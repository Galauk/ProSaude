<?php

class BPATest extends PHPUnit_Framework_TestCase {

    private $object;
    

    //a cada teste passa aqui
    public function setUp() {
        $this->object = new Application_Model_BPA();
        
    }
    
    public function tearDown() {
        $dbTable = new Application_Model_BPA();
        //$db = $dbTable->getAdapter();
       // $db->query("truncate table usuarios");
    }

    public function testCalculaControle() {
        $this->assertEquals(1415, $this->object->calculaControle('0102010072', 30));
        $this->assertEquals(618, $this->object->calculaControle('2613260533', 13));
    }

   // 618
    

    //put your code here
}
