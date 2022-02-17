<?php

namespace Ever_Accounting\Tests\Factories;

class Note_Factory {
	/**
	 * Creates a item in the tests DB.
	 */
	public static function create( $parent_id = '12' , $type  = 'invoice', $note = 'Test Note' ) {
		return \Ever_Accounting\Notes::insert( array(
			'parent_id' => $parent_id,
			'type'      => $type,
			'note'      => $note,
		) );
	}

}
