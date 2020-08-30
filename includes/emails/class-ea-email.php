<?php
/**
 * Main Email Class.
 *
 * This class handles all emails sent through EverAccounting
 *
 * @since       1.0.2
 * @package     EverAccounting
 */

namespace EverAccounting\Emails;

defined( 'ABSPATH' ) || exit();


class Email {
	/**
	 * Email method ID.
	 *
	 * @var String
	 */
	public $id;

	/**
	 * Email method title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * 'yes' if the method is enabled.
	 *
	 * @var string yes, no
	 */
	public $enabled;

	/**
	 * Description for the email.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Default heading.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $heading = '';

	/**
	 * Default subject.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * Plain text template path.
	 *
	 * @var string
	 */
	public $template_plain;

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $template_base;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 * Object this email is for, for example a customer, product, or email.
	 *
	 * @var object|bool
	 */
	public $object;

	/**
	 * Mime boundary (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary;

	/**
	 * Mime boundary header (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary_header;

	/**
	 * True when email is being sent.
	 *
	 * @var bool
	 */
	public $sending;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $plain_replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *
	 * @see $plain_replace
	 * @var array $plain_search
	 */
	public $plain_search = array(
		"/\r/",                                                  // Non-legal carriage return.
		'/&(nbsp|#0*160);/i',                                    // Non-breaking space.
		'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i', // Double quotes.
		'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',               // Single quotes.
		'/&gt;/i',                                               // Greater-than.
		'/&lt;/i',                                               // Less-than.
		'/&#0*38;/i',                                            // Ampersand.
		'/&amp;/i',                                              // Ampersand.
		'/&(copy|#0*169);/i',                                    // Copyright.
		'/&(trade|#0*8482|#0*153);/i',                           // Trademark.
		'/&(reg|#0*174);/i',                                     // Registered.
		'/&(mdash|#0*151|#0*8212);/i',                           // mdash.
		'/&(ndash|minus|#0*8211|#0*8722);/i',                    // ndash.
		'/&(bull|#0*149|#0*8226);/i',                            // Bullet.
		'/&(pound|#0*163);/i',                                   // Pound sign.
		'/&(euro|#0*8364);/i',                                   // Euro sign.
		'/&(dollar|#0*36);/i',                                   // Dollar sign.
		'/&[^&\s;]+;/i',                                         // Unknown/unhandled entities.
		'/[ ]{2,}/',                                             // Runs of spaces, post-handling.
	);

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *
	 * @see $plain_search
	 * @var array $plain_replace
	 */
	public $plain_replace = array(
		'',                                             // Non-legal carriage return.
		' ',                                            // Non-breaking space.
		'"',                                            // Double quotes.
		"'",                                            // Single quotes.
		'>',                                            // Greater-than.
		'<',                                            // Less-than.
		'&',                                            // Ampersand.
		'&',                                            // Ampersand.
		'(c)',                                          // Copyright.
		'(tm)',                                         // Trademark.
		'(R)',                                          // Registered.
		'--',                                           // mdash.
		'-',                                            // ndash.
		'*',                                            // Bullet.
		'£',                                            // Pound sign.
		'EUR',                                          // Euro sign. € ?.
		'$',                                            // Dollar sign.
		'',                                             // Unknown/unhandled entities.
		' ',                                             // Runs of spaces, post-handling.
	);

	/**
	 * Strings to find/replace in subjects/headings.
	 *
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Find/replace.
		$this->placeholders = array_merge(
			array(
				'{site_title}'   => $this->get_blogname(),
				'{site_address}' => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{site_url}'     => wp_parse_url( home_url(), PHP_URL_HOST ),
			),
			$this->placeholders
		);

		// Default template base if not declared in child constructor.
		if ( is_null( $this->template_base ) ) {
			$this->template_base = eaccounting()->plugin_path() . '/templates/';
		}

		$this->email_type = 'html';
		$this->enabled    = 'yes';

		add_action( 'phpmailer_init', array( $this, 'handle_multipart' ) );
	}

	/**
	 * Handle multipart mail.
	 *
	 * @param \PHPMailer $mailer PHPMailer object.
	 *
	 * @return \PHPMailer
	 */
	public function handle_multipart( $mailer ) {
		if ( $this->sending && 'multipart' === $this->get_email_type() ) {
			$mailer->AltBody = wordwrap(
				preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) )
			);
			$this->sending   = false;
		}

		return $mailer;
	}

	/**
	 * Format email string.
	 *
	 * @param mixed $string Text to replace placeholders in.
	 *
	 * @return string
	 */
	public function format_string( $string ) {
		$find    = array_keys( $this->placeholders );
		$replace = array_values( $this->placeholders );

		// Take care of blogname which is no longer defined as a valid placeholder.
		$find[]    = '{blogname}';
		$replace[] = $this->get_blogname();

		/**
		 * Filter for main find/replace.
		 *
		 * @since 1.0.2
		 */
		return apply_filters( 'eaccounting_email_format_string', str_replace( $find, $replace, $string ), $this );
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return $this->subject;
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return $this->heading;
	}

	/**
	 * Default content to show below main email content.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_additional_content() {
		return '';
	}

	/**
	 * Return content from the additional_content field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_additional_content() {
		return apply_filters( 'eaccounting_email_additional_content_' . $this->id, $this->format_string( $this->get_default_additional_content() ), $this->object, $this );
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( 'eaccounting_email_subject_' . $this->id, $this->format_string( $this->get_default_subject() ), $this->object, $this );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_heading() {
		return apply_filters( 'eaccounting_email_heading_' . $this->id, $this->format_string( $this->get_default_heading() ), $this->object, $this );
	}

	/**
	 * Get valid recipients.
	 *
	 * @return string
	 */
	public function get_recipient() {
		$recipient  = apply_filters( 'eaccounting_email_recipient_' . $this->id, $this->recipient, $this->object, $this );
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients = array_filter( $recipients, 'is_email' );

		return implode( ', ', $recipients );
	}

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

		$header .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_address() . ">\r\n";

		return apply_filters( 'eaccounting_email_headers', $header, $this->id, $this->object, $this );
	}

	/**
	 * Get email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return apply_filters( 'eaccounting_email_attachments', array(), $this->id, $this->object, $this );
	}

	/**
	 * Return email type.
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}

	/**
	 * Get email content type.
	 *
	 * @param string $default_content_type Default wp_mail() content type.
	 *
	 * @return string
	 */
	public function get_content_type( $default_content_type = '' ) {
		switch ( $this->get_email_type() ) {
			case 'html':
				$content_type = 'text/html';
				break;
			case 'multipart':
				$content_type = 'multipart/alternative';
				break;
			default:
				$content_type = 'text/plain';
				break;
		}

		return apply_filters( 'eaccounting_email_content_type', $content_type, $this, $default_content_type );
	}

	/**
	 * Return the email's title
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'eaccounting_email_title', $this->title, $this );
	}

	/**
	 * Return the email's description
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'eaccounting_email_description', $this->description, $this );
	}

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get email content.
	 *
	 * @return string
	 */
	public function get_content() {
		$this->sending = true;

		if ( 'plain' === $this->get_email_type() ) {
			$email_content = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) ), 70 );
		} else {
			$email_content = $this->get_content_html();
		}

		return $email_content;
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @param string|null $content Content that will receive inline styles.
	 *
	 * @return string
	 * @version 4.0.0
	 */
	public function style_inline( $content ) {
		if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ), true ) ) {
			ob_start();
			eaccounting_get_template( 'emails/email-styles.php' );
			$css     = apply_filters( 'eaccounting_email_styles', ob_get_clean(), $this );
			$content = '<style type="text/css">' . $css . '</style>' . $content;
		}

		return $content;
	}


	/**
	 * Get the email content in plain text format.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return '';
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return '';
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @param string $from_name Default wp_mail() name associated with the "from" email address.
	 *
	 * @return string
	 */
	public function get_from_name( $from_name = '' ) {
		$from_name = apply_filters( 'eaccounting_email_from_name', get_option( 'eaccounting_email_from_name' ), $this, $from_name );

		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @since 1.0.2
	 *
	 * @param string $from_email Default wp_mail() email address to send from.
	 *
	 * @return string
	 */
	public function get_from_address( $from_email = '' ) {
		$from_email = apply_filters( 'eaccounting_email_from_address', get_option( 'eaccounting_email_from_address' ), $this, $from_email );

		return sanitize_email( $from_email );
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return apply_filters( 'eaccounting_email_enabled_' . $this->id, 'yes' === $this->enabled, $this->object, $this );
	}

	/**
	 * Send an email.
	 *
	 * @since 1.0.2
	 *
	 * @param string $to          Email to.
	 * @param string $subject     Email subject.
	 * @param string $message     Email message.
	 * @param string $headers     Email headers.
	 * @param array  $attachments Email attachments.
	 *
	 * @return bool success
	 */
	public function send( $to, $subject, $message, $headers, $attachments ) {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		var_dump( $message );
		$message = apply_filters( 'eaccounting_mail_content', $this->style_inline( $message ) );

		$return = wp_mail( $to, $subject, $message, $headers, $attachments );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $return;
	}

}
