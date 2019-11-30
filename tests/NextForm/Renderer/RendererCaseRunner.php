<?php
use Abivia\NextForm\Manager;

/**
 * Provides a method that will run a set of test cases
 */
trait RendererCaseRunner {

    /**
     * Run a set of test data against the expected results
     * @param array $cases Array of test cases
     * @param array $expect Array of expected results
     * @param array $skip Optional list of test keys to skip.
     */
    protected function runCases($cases, $expect, $skip = []) {
        $missingExpect = [];
        $notRun = array_combine(array_keys($expect), array_keys($expect));
        foreach ($cases as $key => $info) {
            if (in_array($key, $skip)) {
                continue;
            }
            if (!isset($expect[$key])) {
                $missingExpect[] = $key;
                continue;
            }
            Manager::boot();
            $data = $this->testObj->render($info[0], $info[1]);
            $this->logResult($data, $info[2]);
            $data->onCloseDone = null;
            if ($expect[$key] != $data) {
                $this->logResult($expect[$key], 'Expected: ' . $info[2]);
            }
            $this->assertEquals($expect[$key], $data, $info[2]);
            unset($notRun[$key]);
        }
        $this->assertTrue(
            empty($missingExpect),
            'Cases with no expectation: ' . implode(', ', $missingExpect)
        );
        $this->assertTrue(
            empty($notRun),
            'Expectations with no test case: ' . implode(', ', $notRun)
        );
    }

    /**
     * Run a set of test data against the expected results
     * @param array $cases Array of test cases
     * @param array $expect Array of expected results
     * @param array $skip Optional list of test keys to skip.
     */
    protected function runElementCases($cases, $expect, $skip = []) {
        $missingExpect = [];
        $notRun = array_combine(array_keys($expect), array_keys($expect));
        foreach ($cases as $key => $info) {
            if (in_array($key, $skip)) {
                continue;
            }
            if (!isset($expect[$key])) {
                $missingExpect[] = $key;
                continue;
            }
            Manager::boot();
            $data = $info[0]->render($info[1]);
            $this->logResult($data, $info[2]);
            $data->onCloseDone = null;
            if ($expect[$key] != $data) {
                $this->logResult($expect[$key], 'Expected: ' . $info[2]);
            }
            $this->assertEquals($expect[$key], $data, $info[2]);
            unset($notRun[$key]);
        }
        $this->assertTrue(
            empty($missingExpect),
            'Cases with no expectation: ' . implode(', ', $missingExpect)
        );
        $this->assertTrue(
            empty($notRun),
            'Expectations with no test case: ' . implode(', ', $notRun)
        );
    }

}
