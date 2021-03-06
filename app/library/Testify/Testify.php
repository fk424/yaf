<?php

//namespace Testify;

/**
 * Testify - a micro unit testing framework
 *
 * This is the main class of the framework. Use it like this:
 *
 * @version    0.4
 * @author     Martin Angelov
 * @author     Marc-Olivier Fiset
 * @author     Fabien Salathe
 * @link       marco
 * @throws     TestifyException
 * @license    GPL
 */

class Testify
{
    private $tests = array();
    private $stack = array();
    private $fileCache = array();
    private $currentTestCase;
    private $suiteTitle;
    private $suiteResults;

    private $before = null;
    private $after = null;
    private $beforeEach = null;
    private $afterEach = null;
    private $coverage_path;
    
    //php coverage
    private $php_coverage;
    
    /**
     * A public object for storing state and other variables across test cases and method calls.
     *
     * @var \StdClass
     */
    public $data = null;

    /**
     * The constructor.
     *
     * @param string $title The suite title
     */
    public function __construct($title, $coverage_path = null)
    {
        $this->suiteTitle = $title;
        $this->data = new \StdClass;
        $this->suiteResults = array('pass' => 0, 'fail' => 0);
        
        //引入php_coverage
        if(!empty($coverage_path)){
            $driver_xdebug = new PHP_CodeCoverage_Driver_Xdebug();
            $filter = new PHP_CodeCoverage_Filter();
            global $blackDir;
            foreach ($blackDir as $dir) {
                $filter->addDirectoryToBlacklist($dir);
            }
            $this->php_coverage = new PHP_CodeCoverage($driver_xdebug, $filter);
//            var_dump($this->php_coverage);die;
            $this->coverage_path = $coverage_path;
        }
    }

    /**
     * 结束测试。1.结束结果
     * Enter description here ...
     */
    public function endTest(){
        $this();
        
        if(!empty($this->php_coverage)){
            $writer = new PHP_CodeCoverage_Report_HTML;
            $writer->process($this->php_coverage, $this->coverage_path);        
        }
        
//      $obj = serialize($this->php_coverage);
//      $post = array('obj'=>$obj);
//      //$a = file_get_contents('http://dj_last.test.360.cn/test/index?obj='.$obj);
//      $ch = curl_init();
//      curl_setopt($ch, CURLOPT_URL, 'http://dj_last.test.360.cn/test/index');//设置链接
//      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
//      curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
//      curl_setopt($ch, CURLOPT_HEADER, 0);
//      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//POST数据
//      $response = curl_exec($ch);//接收返回信息
//      
//      
//      //var_dump('http://dj_last.test.360.cn/test/index?obj='.$obj);
//      var_dump($response);echo 123;
//      $writer = new PHP_CodeCoverage_Report_HTML;
//      $writer->process($this->php_coverage, '/tmp/ttt');
    }
    
    
    /**
     * Add a test case.
     *
     * @param string $name Title of the test case
     * @param \function $testCase (optional) The test case as a callback
     *
     * @return $this
     */
    public function test($name, \Closure $testCase = null)
    {
        if (is_callable($name)) {
            $testCase = $name;
            $name = "Test Case #" . (count($this->tests) + 1);
        }

        $this->affirmCallable($testCase, "test");

        $this->tests[] = array("name" => $name, "testCase" => $testCase);
        return $this;
    }

    /**
     * Executed once before the test cases are run.
     *
     * @param \function $callback An anonymous callback function
     */
    public function before(\Closure $callback)
    {
        $this->affirmCallable($callback, "before");
        $this->before = $callback;
    }

    /**
     * Executed once after the test cases are run.
     *
     * @param \function $callback An anonymous callback function
     */
    public function after(\Closure $callback)
    {
        $this->affirmCallable($callback, "after");
        $this->after = $callback;
    }

    /**
     * Executed for every test case, before it is run.
     *
     * @param \function $callback An anonymous callback function
     */
    public function beforeEach(\Closure $callback)
    {
        $this->affirmCallable($callback, "beforeEach");
        $this->beforeEach = $callback;
    }

    /**
     * Executed for every test case, after it is run.
     *
     * @param \function $callback An anonymous callback function
     */
    public function afterEach(\Closure $callback)
    {
        $this->affirmCallable($callback, "afterEach");
        $this->afterEach = $callback;
    }

    /**
     * Run all the tests and before / after functions. Calls {@see report} to generate the HTML report page.
     *
     * @return $this
     */
    public function run()
    {
        $arr = array($this);

        if (is_callable($this->before)) {
            call_user_func_array($this->before, $arr);
        }

        foreach($this->tests as $test) {
            $this->currentTestCase = $test['name'];
            
            if(!empty($this->php_coverage)){
                $this->php_coverage->start($this->suiteTitle);
            }

            if (is_callable($this->beforeEach)) {
                call_user_func_array($this->beforeEach, $arr);
            }
            
            // Executing the testcase
            call_user_func_array($test['testCase'], $arr);

            if (is_callable($this->afterEach)) {
                call_user_func_array($this->afterEach, $arr);
            }
            
            if(!empty($this->php_coverage)){
                $this->php_coverage->stop();
            }
        }

        if (is_callable($this->after)) {
            call_user_func_array($this->after, $arr);
        }

        $this->report();

        return $this;
    }

    /**
     * Alias for {@see assertTrue} method.
     *
     * @param boolean $arg The result of a boolean expression
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     * @see Testify->assertTrue()
     *
     * @return boolean
     */
    public function assert($arg, $message = '')
    {
        return $this->assertTrue($arg, $message);
    }

    /**
     * Passes if given a truthfull expression.
     *
     * @param boolean $arg The result of a boolean expression
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertTrue($arg, $message = '')
    {
        return $this->recordTest($arg == true, $message);
    }

    /**
     * Passes if given a falsy expression.
     *
     * @param boolean $arg The result of a boolean expression
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertFalse($arg, $message = '')
    {
        return $this->recordTest($arg == false, $message);
    }

    /**
     * Passes if $arg1 == $arg2.
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertEquals($arg1, $arg2, $message = '')
    {
        return $this->recordTest($arg1 == $arg2, $message);
    }

    /**
     * Passes if $arg1 != $arg2.
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertNotEquals($arg1, $arg2, $message = '')
    {
        return $this->recordTest($arg1 != $arg2, $message);
    }

    /**
     * Passes if $arg1 === $arg2.
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertSame($arg1, $arg2, $message = '')
    {
        return $this->recordTest($arg1 === $arg2, $message);
    }

    /**
     * Passes if $arg1 !== $arg2.
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertNotSame($arg1, $arg2, $message = '')
    {
        return $this->recordTest($arg1 !== $arg2, $message);
    }

    /**
     * Passes if $arg is an element of $arr.
     *
     * @param mixed $arg
     * @param array $arr
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertInArray($arg, Array $arr, $message = '')
    {
        return $this->recordTest(in_array($arg, $arr), $message);
    }

    /**
     * Passes if $arg is not an element of $arr.
     *
     * @param mixed $arg
     * @param array $arr
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertNotInArray($arg, Array $arr, $message = '')
    {
        return $this->recordTest(!in_array($arg, $arr), $message);
    }

    /**
     * Unconditional pass.
     *
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function pass($message = '')
    {
        return $this->recordTest(true, $message);
    }

    /**
     * Unconditional fail.
     *
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function fail($message = '')
    {
        // This check fails every time
        return $this->recordTest(false, $message);
    }

    /**
     * Generates a pretty CLI or HTML5 report of the test suite status. Called implicitly by {@see run}.
     *
     * @return $this
     */
    public function report()
    {
        $title = $this->suiteTitle;
        $suiteResults = $this->suiteResults;
        $cases = $this->stack;

        if (php_sapi_name() === 'cli') {
            include dirname(__FILE__).'/testify.report.cli.php';
        } else {
            include dirname(__FILE__).'/testify.report.php';
        }

        return $this;
    }

    /**
     * A helper method for recording the results of the assertions in the internal stack.
     *
     * @param boolean $pass If equals true, the test has passed, otherwise failed
     * @param string $message (optional) Custom message
     *
     * @return boolean
     */
    private function recordTest($pass, $message = '')
    {
        if (!array_key_exists($this->currentTestCase, $this->stack) ||
              !is_array($this->stack[$this->currentTestCase])) {

            $this->stack[$this->currentTestCase]['tests'] = array();
            $this->stack[$this->currentTestCase]['pass'] = 0;
            $this->stack[$this->currentTestCase]['fail'] = 0;
        }

        $bt = debug_backtrace();
        $source = $this->getFileLine($bt[1]['file'], $bt[1]['line'] - 1);
        $bt[1]['file'] = basename($bt[1]['file']);

        $result = $pass ? "pass" : "fail";
        $this->stack[$this->currentTestCase]['tests'][] = array(
            "name"      => $message,
            "type"      => $bt[1]['function'],
            "result"    => $result,
            "line"      => $bt[1]['line'],
            "file"      => $bt[1]['file'],
            "source"    => $source
        );

        $this->stack[$this->currentTestCase][$result]++;
        $this->suiteResults[$result]++;

        return $pass;
    }

    /**
     * Internal method for fetching a specific line of a text file. With caching.
     *
     * @param string $file The file name
     * @param integer $line The line number to return
     *
     * @return string
     */
    private function getFileLine($file, $line)
    {
        if (!array_key_exists($file, $this->fileCache)) {
            $this->fileCache[$file] = file($file);
        }

        return trim($this->fileCache[$file][$line]);
    }

    /**
     * Internal helper method for determine whether a variable is callable as a function.
     *
     * @param mixed $callback The variable to check
     * @param string $name Used for the error message text to indicate the name of the parent context
     * @throws TestifyException if callback argument is not a function
     */
    private function affirmCallable(&$callback, $name)
    {
        if (!is_callable($callback)) {
            throw new TestifyException("$name(): is not a valid callback function!");
        }
    }

    /**
     * Alias for {@see assertEquals}.
     *
     * @deprecated Not recommended, use {@see assertEquals}
     * @param mixed $arg1
     * @param mixed $arg2
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertEqual($arg1, $arg2, $message = '')
    {
        return $this->assertEquals($arg1, $arg2, $message);
    }

    /**
     * Alias for {@see assertSame}.
     *
     * @deprecated Not recommended, use {@see assertSame}
     * @param mixed $arg1
     * @param mixed $arg2
     * @param string $message (optional) Custom message. SHOULD be specified for easier debugging
     *
     * @return boolean
     */
    public function assertIdentical($arg1, $arg2, $message = '')
    {
        return $this->recordTest($arg1 === $arg2, $message);
    }

    /**
     * Alias for {@see run} method.
     *
     * @see Testify->run()
     *
     * @return $this
     */
    public function __invoke()
    {
        return $this->run();
    }
}

/**
 * TestifyException class
 *
 */
class TestifyException extends \Exception
{

}
