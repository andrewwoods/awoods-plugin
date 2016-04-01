<?php

class SetupTest extends WP_UnitTestCase {

	function test_setup() {
		// replace this with some actual testing code
		$one_class = 'One';
		$one_expected = AWOODS_DIR_PATH . 'lib/one.php';
		$one_actual = awoods_setup::get_class_path( $one_class );
	
		if ( $one_actual === $one_expected ) {
			$this->assertTrue( true );
		} else {
			echo( "\none_actual={$one_actual}\n");
			echo( "one_expected={$one_expected}\n");
			$this->assertTrue( false );
		}

		
		$two_class = 'Two_Level';
		$two_expected = AWOODS_DIR_PATH . 'lib/two/level.php';
		$two_actual = awoods_setup::get_class_path( $two_class );
	
		if ( $two_actual === $two_expected ) {
			$this->assertTrue( true );
		} else {
			echo( "\ntwo_actual={$two_actual}\n");
			echo( "two_expected={$two_expected}\n");
			$this->assertTrue( false );
		}


		$three_class = 'Awoods_Admin_Project';
		$three_expected = AWOODS_DIR_PATH . 'lib/awoods/admin/project.php';
		$three_actual = awoods_setup::get_class_path( $three_class );
	
		if ( $three_actual === $three_expected ) {
			$this->assertTrue( true );
		} else {
			echo( "\nthree_actual={$three_actual}\n");
			echo( "three_expected={$three_expected}\n");
			$this->assertTrue( false );
		}

	}
}

