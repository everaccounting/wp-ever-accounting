<?php
/**
 * Main Email Class.
 *
 * This class handles all emails sent through EverAccounting
 *
 * @since       1.0.2
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Emails\Email;

defined( 'ABSPATH' ) || exit();


class Emails {

	/**
	 * Array of email notification classes
	 *
	 * @var \EverAccounting\Emails\Email[]
	 */
	public $emails = array();

	/**
	 * The single instance of the class
	 *
	 * @var Emails
	 */
	protected static $_instance = null;

	/**
	 * Main Emails Instance.
	 *
	 * Ensures only one instance of Emails is loaded or can be loaded.
	 *
	 * @since 1.0.2
	 * @static
	 * @return Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks.
		add_action( 'eaccounting_email_header', array( $this, 'email_header' ) );
		add_action( 'eaccounting_email_footer', array( $this, 'email_footer' ) );

		// Hook for replacing {site_title} in email-footer.
		add_filter( 'eaccounting_email_footer_text', array( $this, 'replace_placeholders' ) );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'eaccounting_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include email classes.
		include_once dirname( __FILE__ ) . '/emails/class-ea-email.php';

		$this->emails['Email_New_Payment'] = include 'emails/class-ea-email-new-payment.php';

		$this->emails = apply_filters( 'eaccounting_email_classes', $this->emails );
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return \EverAccounting\Emails\Email[]
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( eaccounting()->settings->get( 'email_from_name' ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return sanitize_email( eaccounting()->settings->get( 'email_from_address' ) );
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading Heading for the email.
	 */
	public function email_header( $email_heading ) {
		eaccounting_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		eaccounting_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Replace placeholder text in strings.
	 *
	 * @since  1.0.2
	 *
	 * @param string $string Email footer text.
	 *
	 * @return string         Email footer text with any replacements done.
	 */
	public function replace_placeholders( $string ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );

		return str_replace(
			array(
				'{site_title}',
				'{site_address}',
				'{site_url}',
				'{everaccounting}',
				'{eaccounting}',
				'{EverAccounting}',
			),
			array(
				$this->get_blogname(),
				$domain,
				$domain,
				'<a href="https://wpeveraccounting.com">EverAccounting</a>',
				'<a href="https://wpeveraccounting.com">EverAccounting</a>',
				'<a href="https://wpeveraccounting.com">EverAccounting</a>',
			),
			$string
		);
	}


	/**
	 * Wraps a message in the mail template.
	 *
	 * @param string $email_heading Heading text.
	 * @param string $message       Email message.
	 * @param bool   $plain_text    Set true to send as plain text. Default to false.
	 *
	 * @return string
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer.
		ob_start();

		do_action( 'eaccounting_email_header', $email_heading, null );

		echo wpautop( wptexturize( $message ) );

		do_action( 'eaccounting_email_footer', null );

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send the email.
	 *
	 * @param mixed  $to          Receiver.
	 * @param mixed  $subject     Email subject.
	 * @param mixed  $message     Message.
	 * @param string $headers     Email headers (default: "Content-Type: text/html\r\n").
	 * @param string $attachments Attachments (default: "").
	 *
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		// Send.
		$email = new Email();

		return $email->send( $to, $subject, $message, $headers, $attachments );
	}


}
