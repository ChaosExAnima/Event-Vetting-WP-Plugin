<?php
/**
 * Application post type class.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

use WP_Error;

class Application {

	const POST_TYPE = 'ev_application'; // Needs to be shorter than 20 characters.

	const STATUS_PENDING = 'ev_pending';

	const STATUS_APPROVED = 'ev_approved';

	const STATUS_DENIED = 'ev_denied';

	const META_INFO = 'event_vetting_application_data';

	const META_VOTES = 'event_vetting_app_votes';

	/**
	 * Admin class reference.
	 *
	 * @var Admin
	 */
	protected $admin;

	/**
	 * Constructor.
	 *
	 * @param Admin $admin Admin page reference.
	 */
	public function __construct( Admin $admin ) {
		$this->admin = $admin;
	}

	/**
	 * Registers the post type.
	 *
	 * @return void
	 */
	public function init() {
		$labels = [
			'name'               => __( 'Applications', 'event-vetting' ),
			'singular_name'      => __( 'Application', 'event-vetting' ),
			'add_new_item'       => __( 'Add New Application', 'event-vetting' ),
			'edit_item'          => __( 'Edit Application', 'event-vetting' ),
			'new_item'           => __( 'New Application', 'event-vetting' ),
			'view_item'          => __( 'View Application', 'event-vetting' ),
			'view_items'         => __( 'View Application', 'event-vetting' ),
			'search_items'       => __( 'Search Applications', 'event-vetting' ),
			'not_found'          => __( 'No Applications found', 'event-vetting' ),
			'not_found_in_trash' => __( 'No Applications found in trash', 'event-vetting' ),
			'all_items'          => __( 'Applications', 'event-vetting' ),
		];
		register_post_type( self::POST_TYPE, [
			'labels'            => $labels,
			'description'       => __( 'Applications', 'event-vetting' ),
			'show_ui'           => true,
			'show_in_menu'      => $this->admin->get_menu_slug(),
			'show_in_rest'      => false,
			'show_in_admin_bar' => false,
			'supports'          => [ 'thumbnail' ],
			'rewrite'           => false,
			'capabilities'      => [
				'create_posts'  => 'do_not_allow',
				'delete_post'   => 'do_not_allow',
				'edit_posts'    => Roles::VETTER_CAP,
				'edit_post'     => Roles::VETTER_CAP,
				'publish_posts' => Roles::VETTER_CAP,
			],
		] );

		register_post_status( self::STATUS_PENDING, [
			'label'                     => __( 'Pending', 'event-vetting' ),
			'public'                    => false,
			'internal'                  => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop(
				'Pending <span class="count">(%s)</span>',
				'Pending <span class="count">(%s)</span>',
				'event-vetting'
			),
		] );

		register_post_status( self::STATUS_APPROVED, [
			'label'                     => __( 'Approved', 'event-vetting' ),
			'public'                    => false,
			'internal'                  => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop(
				'Approved <span class="count">(%s)</span>',
				'Approved <span class="count">(%s)</span>',
				'event-vetting'
			),
		] );

		register_post_status( self::STATUS_DENIED, [
			'label'                     => __( 'Denied', 'event-vetting' ),
			'public'                    => false,
			'internal'                  => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop(
				'Denied <span class="count">(%s)</span>',
				'Denied <span class="count">(%s)</span>',
				'event-vetting'
			),
		] );
	}

	/**
	 * Creates a new application.
	 *
	 * @param array $input_data The input information.
	 * @return integer|WP_Error Post ID of new application, or application issue.
	 */
	public function create( array $input_data ) {
		/**
		 * Allows filtering of input data before validation.
		 *
		 * @since 1.0.0
		 *
		 * @param array $input_data The input data being processed.
		 */
		$input_data = apply_filters( 'event_vetting_application_pre_create', $input_data );
		if ( empty( $input_data['email'] ) ) {
			return new WP_Error( 'no-email', __( 'No email provided.', 'event-vetting' ), [ 'status' => 400 ] );
		} elseif ( ! is_email( $input_data['email'] ) ) {
			return new WP_Error( 'invalid-email', __( 'Invalid email provided.', 'event-vetting' ), [ 'status' => 400 ] );
		} elseif ( empty( $input_data['name'] ) ) {
			return new WP_Error( 'no-name', __( 'No name provided.', 'event-vetting' ), [ 'status' => 400 ] );
		}

		$sanitized_data = [];
		foreach ( $input_data as $input_field => $input_value ) {
			$sanitized_function             = apply_filters(
				"event_vetting_application_sanitize_${input_field}",
				'sanitize_text_field'
			);
			$sanitized_data[ $input_field ] = $sanitized_function( $input_value );
		}

		$existing_post = self::get_application_by_email( $sanitized_data['email'] );
		if ( 0 !== $existing_post ) {
			return new WP_Error( 'existing-email', __( 'Email part of existing application.', 'event-vetting' ), [ 'status' => 400 ] );
		}

		$application_id = wp_insert_post( [
			'post_type'    => self::POST_TYPE,
			'post_title'   => $sanitized_data['name'],
			'post_content' => $sanitized_data['email'],
			'post_status'  => self::STATUS_PENDING,
			'meta_input'   => [
				[ self::META_INFO ] => $sanitized_data,
			],
		], true );
		delete_transient( "event_vetting_application_email_key_{$sanitized_data['email']}" );

		return $application_id;
	}

	/**
	 * Gets voting options available.
	 *
	 * @return array
	 */
	public static function get_voting_options() : array {
		/**
		 * Filters application voting options.
		 *
		 * @since 1.0.0
		 *
		 * @param array $options Array of voting options, keyed by slug
		 * and value of text.
		 */
		return apply_filters( 'event_vetting_application_voting_options', [
			'yes'   => __( 'Approve', 'event-vetting' ),
			'maybe' => __( 'Tentatively Approve', 'event-vetting' ),
			'deny'  => __( 'Deny', 'event-vetting' ),
		] );
	}

	/**
	 * Gets votes associated with a given application
	 *
	 * @param int $post_id The post to check.
	 * @return array
	 */
	public static function get_votes( int $post_id ) : array {
		$votes = get_post_meta( $post_id, self::META_VOTES, true );
		if ( '' !== $votes ) {
			return (array) $votes;
		}
		return [];
	}

	/**
	 * Submits a vote for a given user.
	 *
	 * @param int    $user_id The user ID that voted.
	 * @param int    $post_id The application post object or ID.
	 * @param string $vote    The vote from the user.
	 * @return bool|WP_Error  True for success, false for already cast, error in other cases.
	 */
	public static function submit_vote( int $user_id, int $post_id, string $vote ) {
		$vote_options = self::get_voting_options();
		$vote_keys    = array_keys( $vote_options );
		if ( ! in_array( $vote, $vote_keys, true ) ) {
			return new WP_Error( 'invalid-vote', __( 'Invalid vote cast.', 'event-vetting' ) );
		}

		if ( self::STATUS_PENDING !== get_post_status( $post_id ) ) {
			return new WP_Error( 'not-pending', __( 'This application is not pending.', 'event-vetting' ) );
		}

		if ( ! user_can( $user_id, Roles::VETTER_CAP, $post_id ) ) {
			return new WP_Error( 'invalid-perms', __( 'You cannot vote.', 'event-vetting' ) );
		}

		$current_votes = self::get_votes( $post_id );
		$updated_vote  = false;
		if ( isset( $current_votes[ $user_id ] ) ) {
			$updated_vote = true;
		}
		$current_votes[ $user_id ] = $vote;

		update_post_meta( $post_id, self::META_VOTES, $current_votes );

		$user = get_userdata( $user_id );
		if ( ! $updated_vote ) {
			$message = sprintf(
				__( 'Vetter %1$s voted for %2$s.', 'event-vetting' ),
				$user->display_name,
				$vote_options[ $vote ]
			);
		} else {
			$message = sprintf(
				__( 'Vetter %1$s changed their vote to %2$s.', 'event-vetting' ),
				$user->display_name,
				$vote_options[ $vote ]
			);
		}
		Notes::add_system_note( $post_id, $message );

		return $updated_vote;
	}

	/**
	 * Resets an application to pending state.
	 *
	 * @param integer $post_id The application ID.
	 * @return bool|WP_Error   True for success, false for already reset, error in other cases.
	 */
	public static function reset( int $post_id ) {
		$application = get_post( $post_id );
		if ( self::POST_TYPE !== $application->post_type ) {
			return new WP_Error( 'not-application', __( 'Post is not an application.', 'event-vetting' ) );
		}
		if ( self::STATUS_PENDING === $application->post_status ) {
			return false;
		}

		if ( current_user_can( 'administrator' ) ) {
			return new WP_Error( 'user-perms', __( 'Must be an administrator to reset applications.', 'event-vetting' ) );
		}

		$application->post_status = self::STATUS_PENDING;

		$result = wp_update_post( $application, true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return (bool) $result;
	}

	/**
	 * Gets an application by email.
	 *
	 * @param string $email The email to check.
	 * @return integer      Post ID, or zero on failure.
	 */
	public static function get_application_by_email( string $email ) : int {
		$cache_key = "event_vetting_application_email_key_${email}";
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return (int) $cached;
		}

		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$post_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID from {$wpdb->posts} WHERE post_content = %s AND post_type = %s LIMIT 1",
			$email,
			self::POST_TYPE
		) );

		if ( null === $post_id ) {
			set_transient( $cache_key, 0, 15 * MINUTE_IN_SECONDS );
			return 0;
		}

		set_transient( $cache_key, (int) $post_id, DAY_IN_SECONDS );
		return (int) $post_id;
	}
}
