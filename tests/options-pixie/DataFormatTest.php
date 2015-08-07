<?php

class DataFormatTest extends \WP_UnitTestCase {
	/**
	 * Check that is_json function exists (as a static function).
	 */
	public function test_is_json_exists() {
		$this->assertTrue( method_exists( 'Options_Pixie_Data_Format', 'is_json' ) );
	}

	/**
	 * @depends test_is_json_exists
	 */
	public function test_is_json_likes_simple_json() {
		$input  = '{"string": "some text", "int": 123}';
		$result = Options_Pixie_Data_Format::is_json( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_json_exists
	 */
	public function test_is_json_dislikes_serialized() {
		$input  = 'a:3:{s:13:"twentyfifteen";s:7:"/themes";s:14:"twentyfourteen";s:7:"/themes";s:14:"twentythirteen";s:7:"/themes";}';
		$result = Options_Pixie_Data_Format::is_json( $input );
		$this->assertFalse( $result );
	}

	/**
	 * Check that is_expandable function exists (as a static function).
	 */
	public function test_is_expandable_exists() {
		$this->assertTrue( method_exists( 'Options_Pixie_Data_Format', 'is_expandable' ) );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_dislikes_simple_json() {
		$input  = '{"string": "some text", "int": 123}';
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertFalse( $result );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_likes_json_with_list() {
		$input  = '{"string": "some text", "int": 123, "list": ["one", "two", "three"]}';
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_likes_json_with_nested_map() {
		$input  = '{"string": "some text", "int": 123, "map": {"one": 1, "two": 2, "three": 3}}';
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_dislikes_simple_serialized() {
		$input  = 'a:3:{s:13:"twentyfifteen";s:7:"/themes";s:14:"twentyfourteen";s:7:"/themes";s:14:"twentythirteen";s:7:"/themes";}';
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertFalse( $result );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_likes_serialized_with_nested_array() {
		$input  = 'a:3:{s:6:"widget";a:3:{s:4:"name";s:6:"widget";s:4:"slug";s:6:"widget";s:5:"count";s:4:"5223";}s:4:"post";a:3:{s:4:"name";s:4:"Post";s:4:"slug";s:4:"post";s:5:"count";s:4:"3269";}s:6:"plugin";a:3:{s:4:"name";s:6:"plugin";s:4:"slug";s:6:"plugin";s:5:"count";s:4:"3204";}}';
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_dislikes_simple_array() {
		$input  = array( "one" => 1, "two" => 2, "three" => 3 );
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertFalse( $result );
	}

	/**
	 *
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_likes_array_with_nested_arrays() {
		$input  = array(
			"numbers" => array( "one" => 1, "two" => 2, "three" => 3 ),
			"letters" => array( "one" => "A", "two" => "B", "three" => "C" ),
		);
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_expandable_exists
	 */
	public function test_is_expandable_likes_array_with_nested_arrays_of_arrays() {
		$input  = array(
			"numbers" => array( "one" => 1, "two" => 2, "three" => 3 ),
			"letters" => array( "one" => array( "A", "a" ), "two" => array( "B", "b" ), "three" => array( "C", "c" ) ),
		);
		$result = Options_Pixie_Data_Format::is_expandable( $input );
		$this->assertTrue( $result );
	}

	/**
	 * Check that is_broken_serialized function exists (as a static function).
	 */
	public function test_is_broken_serialized_exists() {
		$this->assertTrue( method_exists( 'Options_Pixie_Data_Format', 'is_broken_serialized' ) );
	}

	/**
	 * @depends test_is_broken_serialized_exists
	 */
	public function test_is_broken_serialized_with_broken_serialized() {
		$input  = 'a:1:{s:1:"two";s:2:"four";}';
		$result = Options_Pixie_Data_Format::is_broken_serialized( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_broken_serialized_exists
	 */
	public function test_is_broken_serialized_with_clean_serialized() {
		$input  = 'a:1:{s:3:"two";s:4:"four";}';
		$result = Options_Pixie_Data_Format::is_broken_serialized( $input );
		$this->assertFalse( $result );
	}

	/**
	 * Check that is_base64 function exists (as a static function).
	 */
	public function test_is_base64_exists() {
		$this->assertTrue( method_exists( 'Options_Pixie_Data_Format', 'is_base64' ) );
	}

	/**
	 * @depends test_is_base64_exists
	 */
	public function test_is_base64_likes_encoded_serialized_array() {
		$input  = array( "one" => 1, "two" => 2, "three" => 3 );
		$input = serialize( $input );
		$input = base64_encode( $input );
		$result = Options_Pixie_Data_Format::is_base64( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_base64_exists
	 */
	public function test_is_base64_likes_encoded_serialized_object() {
		$input  = new stdClass();
		$input->Hello = 'World';
		$input = serialize( $input );
		$input = base64_encode( $input );
		$result = Options_Pixie_Data_Format::is_base64( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_base64_exists
	 */
	public function test_is_base64_likes_encoded_serialized_json() {
		$input  = '{"string": "some text", "int": 123}';
		$input = base64_encode( $input );
		$result = Options_Pixie_Data_Format::is_base64( $input );
		$this->assertTrue( $result );
	}

	/**
	 * @depends test_is_base64_exists
	 */
	public function test_is_base64_dislikes_encoded_string() {
		$input  = 'I am a string.';
		$input = base64_encode( $input );
		$result = Options_Pixie_Data_Format::is_base64( $input );
		$this->assertFalse( $result );
	}

	/**
	 * Check that get_data_types function exists (as a static function).
	 */
	public function test_get_data_types_exists() {
		$this->assertTrue( method_exists( 'Options_Pixie_Data_Format', 'get_data_types' ) );
	}

	/**
	 * @depends test_get_data_types_exists
	 */
	public function test_get_data_types_with_base64_encoded_serialized() {
		$input  = array( "one" => 1, "two" => 2, "three" => 3 );
		$input = serialize( $input );
		$input = base64_encode( $input );
		$result = Options_Pixie_Data_Format::get_data_types( $input );
		$this->assertContains( 'b64', $result, 'contains b64' );
		$this->assertContains( 'S', $result, 'contains S' );
	}

	/**
	 * @depends test_get_data_types_exists
	 */
	public function test_get_data_types_with_object() {
		$input  = new stdClass();
		$input->Hello = 'World';
		$result = Options_Pixie_Data_Format::get_data_types( $input );
		$this->assertContains( 'O', $result, 'contains O' );
	}

	/**
	 * @depends test_get_data_types_exists
	 */
	public function test_get_data_types_base64_encoded_json() {
		$input  = '{"string": "some text", "int": 123}';
		$input = base64_encode( $input );
		$result = Options_Pixie_Data_Format::get_data_types( $input );
		$this->assertContains( 'b64', $result, 'contains b64' );
		$this->assertContains( 'J', $result, 'contains J' );
	}

	/**
	 * @depends test_get_data_types_exists
	 */
	public function test_get_data_types_with_broken_serialized() {
		$input  = 'a:1:{s:1:"two";s:2:"four";}';
		$result = Options_Pixie_Data_Format::get_data_types( $input );
		$this->assertContains( 'S', $result, 'contains S' );
		$this->assertContains( '!!!', $result, 'contains !!!' );
	}
}
