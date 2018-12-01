<?php
/**
 * Class SampleTest
 *
 * @package Simple_Comment_Editing_Options
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}

	function test_sample_string() {
 
		$string = 'Unit and tests are sweet';
	 
		$this->assertEquals( 'Unit tests are sweet', $string );
	}
}
