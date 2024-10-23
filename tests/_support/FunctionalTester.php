<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * Define custom actions here
     */
	/**
	 * Get a random array key.
	 *
	 * @param array $array
	 *
	 * @return mixed
	 */
	public function getRandomElement( $array ) {
		$keys = array_keys( $array );
		$key  = array_rand( $keys );

		return $keys[ $key ];
	}
}
