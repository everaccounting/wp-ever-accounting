<?php
/**
 * Main Email Class.
 *
 * This class handles all emails sent through EverAccounting
 *
 * @package     EverAccounting
 * @since       1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();


class Emails {

	/**
	 * Holds the from address
	 *
	 * @since 1.0.2
	 */
	private $from_address;

	/**
	 * Holds the from name
	 *
	 * @since 1.0.2
	 */
	private $from_name;

	/**
	 * Holds the email content type
	 *
	 * @since 1.0.2
	 */
	private $content_type;

	/**
	 * Holds the email headers
	 *
	 * @since 1.0.2
	 */
	private $headers;

	/**
	 * Whether to send email in HTML
	 *
	 * @since 1.0.2
	 */
	private $html = true;

	/**
	 * The email template to use
	 *
	 * @since 1.0.2
	 */
	private $template;

	/**
	 * The header text for the email
	 *
	 * @since 1.0.2
	 */
	private $heading = '';

	/**
	 * Container for storing all tags
	 *
	 * @since 1.0.2
	 */
	private $tags;

	/**
	 * Get things going
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function __construct() {

		if ( 'none' === $this->get_template() ) {
			$this->html = false;
		}

		add_action( 'eaccounting_email_send_before', array( $this, 'send_before' ) );
		add_action( 'eaccounting_email_send_after', array( $this, 'send_after' ) );
	}


	/**
	 * Set a property
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function __set( $key, $value ) {
		$this->$key = $value;
	}


	/**
	 * Get the email from name
	 *
	 * @return string The email from name
	 * @since 1.0.2
	 */
	public function get_from_name() {

		if ( ! $this->from_name ) {
			$this->from_name = eaccounting()->settings->get( 'from_name', get_bloginfo( 'name' ) );
		}

		/**
		 * Filters the From name for sending emails.
		 *
		 * @param string $name Email From name.
		 * @param Emails $this Email class instance.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_from_name', wp_specialchars_decode( $this->from_name ), $this );
	}


	/**
	 * Get the email from address
	 *
	 * @return string The email from address
	 * @since 1.0.2
	 */
	public function get_from_address() {
		if ( ! $this->from_address ) {
			$this->from_address = eaccounting()->settings->get( 'from_email', get_option( 'admin_email' ) );
		}

		/**
		 * Filters the From email for sending emails.
		 *
		 * @param string $from_address Email address to send from.
		 * @param Emails $this Email class instance.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_from_address', $this->from_address, $this );
	}


	/**
	 * Get the email content type
	 *
	 * @return string The email content type
	 * @since 1.0.2
	 */
	public function get_content_type() {
		if ( ! $this->content_type && $this->html ) {
			$this->content_type = apply_filters( 'eaccounting_email_default_content_type', 'text/html', $this );
		} elseif ( ! $this->html ) {
			$this->content_type = 'text/plain';
		}

		return apply_filters( 'eaccounting_email_content_type', $this->content_type, $this );
	}


	/**
	 * Get the email headers
	 *
	 * @return string The email headers
	 * @since 1.0.2
	 */
	public function get_headers() {
		if ( ! $this->headers ) {
			$this->headers = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
			$this->headers .= "Reply-To: {$this->get_from_address()}\r\n";
			$this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
		}

		/**
		 * Filters the headers sent when sending emails.
		 *
		 * @param array $headers Array of constructed headers.
		 * @param \Affiliate_WP_Emails $this Email class instance.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_headers', $this->headers, $this );
	}


	/**
	 * Retrieves email templates.
	 *
	 * @return array The email templates.
	 * @since 1.0.2
	 *
	 */
	public function get_templates() {
		$templates = array(
			'default' => __( 'Default Template', 'wp-ever-accounting' ),
			'none'    => __( 'No template, plain text only', 'wp-ever-accounting' )
		);

		/**
		 * Filters the list of email templates.
		 *
		 * @param array $templates Key/value pairs of templates where the key is the slug
		 *                         and the value is the translatable label.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_templates', $templates );
	}


	/**
	 * Get the enabled email template
	 *
	 * @return string|null
	 * @since 1.0.2
	 */
	public function get_template() {
		if ( ! $this->template ) {
			$this->template = affiliate_wp()->settings->get( 'email_template', 'default' );
		}

		/**
		 * Filters the template for the current email.
		 *
		 * @param string $template Current template slug.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_template', $this->template );
	}


	/**
	 * Get the header text for the email
	 *
	 * @return string The header text
	 * @since 1.0.2
	 */
	public function get_heading() {
		/**
		 * Filters the header text for the current email.
		 *
		 * @param string $heading Header text.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_heading', $this->heading );
	}


	/**
	 * Build the email
	 *
	 * @param string $message The email message
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function build_email( $message ) {

		if ( false === $this->html ) {
			/**
			 * Filters the message contents of the current email.
			 *
			 * @param string $message Email message contents.
			 * @param \Affiliate_WP_Emails $this Email class instance.
			 *
			 * @since 1.0.2
			 *
			 */
			return apply_filters( 'eaccounting_email_message', wp_strip_all_tags( $message ), $this );
		}

		ob_start();

		affiliate_wp()->templates->get_template_part( 'emails/header', $this->get_template(), true );

		/**
		 * Hooks into the email header
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_email_header', $this );


		affiliate_wp()->templates->get_template_part( 'emails/body', $this->get_template(), true );

		/**
		 * Hooks into the email body
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_email_body', $this );

		affiliate_wp()->templates->get_template_part( 'emails/footer', $this->get_template(), true );

		/**
		 * Hooks into the email footer
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_email_footer', $this );

		$body    = ob_get_clean();
		$message = str_replace( '{email}', $message, $body );

		/** This filter is documented in includes/emails/class-affwp-emails.php */
		return apply_filters( 'eaccounting_email_message', $message, $this );
	}

	/**
	 * Send the email
	 *
	 * @param string $to The To address
	 * @param string $subject The subject line of the email
	 * @param string $message The body of the email
	 * @param string|array $attachments Attachments to the email
	 *
	 * @since 1.0.2
	 */
	public function send( $to, $subject, $message, $attachments = '' ) {

		if ( ! did_action( 'init' ) && ! did_action( 'admin_init' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'You cannot send emails with AffWP_Emails until init/admin_init has been reached', 'wp-ever-accounting' ), null );

			return false;
		}

		// Don't send anything if emails have been disabled
		if ( $this->is_email_disabled() ) {
			return false;
		}

		$this->setup_email_tags();

		/**
		 * Hooks before email is sent
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_email_send_before', $this );

		$message = $this->build_email( $message );

		$message = $this->parse_tags( $message );

		$message = $this->text_to_html( $message );

		/**
		 * Filters the attachments for the current email (if any).
		 *
		 * @param array $attachments Attachments for the email (if any).
		 * @param \Affiliate_WP_Emails $this Email class instance.
		 *
		 * @since 1.0.2
		 *
		 */
		$attachments = apply_filters( 'eaccounting_email_attachments', $attachments, $this );

		$sent = wp_mail( $to, $subject, $message, $this->get_headers(), $attachments );

		/**
		 * Hooks after the email is sent
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_email_send_after', $this );

		return $sent;
	}


	/**
	 * Add filters/actions before the email is sent
	 *
	 * @since 1.0.2
	 */
	public function send_before() {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}


	/**
	 * Remove filters/actions after the email is sent
	 *
	 * @since 1.0.2
	 */
	public function send_after() {
		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		// Reset heading to an empty string
		$this->heading = '';
	}


	/**
	 * Converts text formatted HTML. This is primarily for turning line breaks into <p> and <br/> tags.
	 *
	 * @since 1.0.2
	 * @since 2.2.17 Adjusted the `wpautop()` call to no longer convert line breaks
	 */
	public function text_to_html( $message ) {
		if ( 'text/html' === $this->content_type || true === $this->html ) {
			$message = wpautop( make_clickable( $message ), false );
			$message = str_replace( '&#038;', '&amp;', $message );
		}

		return $message;
	}

	/**
	 * Search content for email tags and filter email tags through their hooks
	 *
	 * @param string $content Content to search for email tags
	 * @param int $affiliate_id The affiliate ID
	 *
	 * @return string $content Filtered content
	 * @since 1.0.2
	 */
	private function parse_tags( $content ) {

		// Make sure there's at least one tag
		if ( empty( $this->tags ) || ! is_array( $this->tags ) ) {
			return $content;
		}

		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_tag' ), $content );

		return $new_content;
	}

	/**
	 * Setup all registered email tags
	 *
	 * @return void
	 * @since 1.0.2
	 */
	private function setup_email_tags() {

		$tags = $this->get_tags();

		foreach ( $tags as $tag ) {
			if ( isset( $tag['function'] ) && is_callable( $tag['function'] ) ) {
				$this->tags[ $tag['tag'] ] = $tag;
			}
		}

	}

	/**
	 * Retrieve all registered email tags
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_tags() {

		// Setup default tags array
		$email_tags = array(
			array(
				'tag'         => 'name',
				'description' => __( 'The display name of the affiliate, as set on the affiliate\'s user profile', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_name'
			),
			array(
				'tag'         => 'user_name',
				'description' => __( 'The user name of the affiliate on the site', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_user_name'
			),
			array(
				'tag'         => 'user_email',
				'description' => __( 'The email address of the affiliate', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_user_email'
			),
			array(
				'tag'         => 'website',
				'description' => __( 'The website of the affiliate', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_website'
			),
			array(
				'tag'         => 'promo_method',
				'description' => __( 'The promo method used by the affiliate', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_promo_method'
			),
			array(
				'tag'         => 'rejection_reason',
				'description' => __( 'The reason an affiliate was rejected', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_rejection_reason'
			),
			array(
				'tag'         => 'login_url',
				'description' => __( 'The affiliate login URL to your website', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_login_url'
			),
			array(
				'tag'         => 'amount',
				'description' => __( 'The amount of a given referral', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_amount'
			),
			array(
				'tag'         => 'site_name',
				'description' => __( 'Your site name', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_site_name'
			),
			array(
				'tag'         => 'referral_url',
				'description' => __( 'The affiliate&#8217;s referral URL', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_referral_url'
			),
			array(
				'tag'         => 'affiliate_id',
				'description' => __( 'The affiliate&#8217;s ID', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_affiliate_id'
			),
			array(
				'tag'         => 'referral_rate',
				'description' => __( 'The affiliate&#8217;s referral rate', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_referral_rate'
			),
			array(
				'tag'         => 'review_url',
				'description' => __( 'The URL to the review page for a pending affiliate', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_review_url'
			),
			array(
				'tag'         => 'landing_page',
				'description' => __( 'The URL the customer landed on that led to a referral being created', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_get_landing_page'
			),
			array(
				'tag'         => 'campaign_name',
				'description' => __( 'The name of the campaign associated with the referral (if any)', 'wp-ever-accounting' ),
				'function'    => 'eaccounting_email_tag_campaign_name'
			),
		);

		/**
		 * Filters the supported email tags and their attributes.
		 *
		 * @param array $email_tags {
		 *     Email tags and their attributes
		 *
		 * @type string $tag Email tag slug.
		 * @type string $description Translatable description for what the email tag represents.
		 * @type callable $function Callback function for rendering the email tag.
		 * }
		 *
		 * @param \Affiliate_WP_Emails $this Email class instance.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_email_tags', $email_tags, $this );

	}

	/**
	 * Parse a specific tag.
	 *
	 * @param $m Message
	 *
	 * @since 1.0.2
	 */
	private function do_tag( $m ) {

		// Get tag
		$tag = $m[1];

		// Return tag if not set
		if ( ! $this->email_tag_exists( $tag ) ) {
			return $m[0];
		}

		return call_user_func( $this->tags[ $tag ]['function'], $this->affiliate_id, $this->referral, $tag );
	}

	/**
	 * Check if $tag is a registered email tag
	 *
	 * @param string $tag Email tag that will be searched
	 *
	 * @return bool True if exists, false otherwise
	 * @since 1.0.2
	 */
	public function email_tag_exists( $tag ) {
		return array_key_exists( $tag, $this->tags );
	}

	/**
	 * Check if all emails should be disabled
	 *
	 * @return bool
	 * @since  2.2 Modified to use eaccounting_get_enabled_email_notifications()
	 *
	 * @since  1.0.2
	 */
	public function is_email_disabled() {

		$disabled = false;

		$enabled_email_notifications = eaccounting_get_enabled_email_notifications();

		// Emails are deemed disabled if no notifications are enabled.
		if ( empty( $enabled_email_notifications ) ) {
			$disabled = true;
		}

		/**
		 * Filters whether to disable all emails.
		 *
		 * @param bool $disabled Whether to disable emails
		 *
		 * @since 1.0.2
		 *
		 */
		return (bool) apply_filters( 'eaccounting_disable_all_emails', $disabled );

	}

}
