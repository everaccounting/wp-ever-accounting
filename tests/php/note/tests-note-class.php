<?php
/**
 * Ever_Accounting Note Class Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Note_Class
 */

namespace Ever_Accounting\Tests;

use Ever_Accounting\Notes;
use Ever_Accounting\Tests\Factories\Note_Factory;

/**
 * Class Tests_Note_Class
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Note_Class extends \WP_UnitTestCase {
	public function test_create_note() {
		$note = Notes::insert( array(
			'parent_id'  => '12',
			'type'  => 'invoice',
			'note' => 'Test Note',
		) );

		$this->assertNotFalse( $note->exists() );

		$this->assertEquals( '12', $note->get_parent_id() );
		$this->assertNotNull( $note->get_id() );
		$this->assertEquals( 'invoice', $note->get_type() );
		$this->assertEquals( 'Test Note', $note->get_note() );
	}

	public function test_update_note() {
		$note = Notes::insert( array(
			'parent_id'  => '12',
			'type'  => 'invoice',
			'note' => 'Test Note',
		) );

		$this->assertNotFalse( $note->exists() );

		$this->assertEquals( '12', $note->get_parent_id() );
		$this->assertNotNull( $note->get_id() );
		$this->assertEquals( 'invoice', $note->get_type() );
		$this->assertEquals( 'Test Note', $note->get_note() );

		$error = Notes::insert( array(
			'id' => $note->get_id(),
			'parent_id'  => '12',
			'type'  => 'invoice',
			'note' => 'Test Note Updated',
		) );

		$this->assertNotWPError( $error );

		$note_id = $note->get_id();
		$note = Notes::get( $note_id ); // so we can read fresh copies from the DB

		$this->assertEquals( '12', $note->get_parent_id() );
		$this->assertNotNull( $note->get_id() );
		$this->assertEquals( 'invoice', $note->get_type() );
		$this->assertEquals( 'Test Note Updated', $note->get_note() );
	}

	public function test_delete_note() {
		$note = Note_Factory::create();
		$this->assertNotEquals( 0, $note->get_id() );
		$this->assertNotFalse( Notes::delete( $note->get_id() ) );
	}

	public function test_exception_note() {
		$note = Notes::insert( array(
			'parent_id' => '',
			'type' => 'bill',
			'note' => 'Test Note'
		) );
		$this->assertEquals( 'Note parent_id is required.', $note->get_error_message() );

		$note = Notes::insert( array(
			'parent_id' => '13',
			'type' => '',
			'note' => 'Test Note'
		) );
		$this->assertEquals( 'Note type is required.', $note->get_error_message() );

		$note = Notes::insert( array(
			'parent_id' => '123',
			'type' => 'bill',
			'note' => ''
		) );
		$this->assertEquals( 'Note note is required.', $note->get_error_message() );


		// just use this when needs to test duplicate categories
//		$category = Categories::insert( array(
//			'name' => 'Expense',
//			'type' => 'expense',
//		) );
//		$this->assertEquals( 'Could not insert item into the database.', $category->get_error_message() );

	}
}
