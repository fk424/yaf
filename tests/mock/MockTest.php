<?php
use \Mockery as m;
Class MockTest extends Yaf_Controller_TestCase {

    public function tearDown() {
        m::close();
    }
    /**
     */
    public function testreadTemp() {
        // $service = m::mock('haha');
        // $service->shouldReceive('readTemp')->times(3)->andReturn(10,12,14);
        // echo $service->readTemp()."\n";
        // echo $service->readTemp()."\n";
        // echo $service->readTemp()."\n";
    }

    public function testSimpleMock() {
//        $mock = m::mock(array('foo'=>1,'bar'=>2));
//        $mock = m::mock('foo', array('foo'=>1,'bar'=>2));
        // $mock = m::mock('foo', function($mock) {
        //     $mock->shouldReceive('foo');
        // });
$mock = \Mockery::mock('stdClass');
//        $mock->shouldReceive('foo')->with(m::any())->once()->andReturn(10);
//        echo $mock->(4)."\n";
//        echo $mock->bar(3)."\n";
    }


}
