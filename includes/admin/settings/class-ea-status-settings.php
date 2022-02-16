<?php

class EA_Status_Settings extends \Ever_Accounting\Admin\Settings_API {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'status';
		$this->label = __( 'Status', 'text_domain' );
	}


}

return new EA_Status_Settings();
