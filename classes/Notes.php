<?php
/**
 * Notes class that manages users adding notes to
 * applications and users.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Notes {
	const COMMENT_USER_NOTE   = 'ev_user_note';
	const COMMENT_SYSTEM_NOTE = 'ev_system_note';

	/**
	 * Gets all associated notes for an application.
	 *
	 * @param integer $post_id Application post ID.
	 * @return array           Array of WP_Comments.
	 */
	public static function get_notes_for_application( int $post_id ) : array {
		return get_comments( [
			'type'         => [ self::COMMENT_USER_NOTE, self::COMMENT_SYSTEM_NOTE ],
			'post_id'      => $post_id,
			'post_type'    => Application::POST_TYPE,
			'hierarchical' => false,
		] );
	}

	/**
	 * Adds a user-written note to a given post.
	 *
	 * @param integer $post_id The post ID.
	 * @param integer $user_id The user writing the note.
	 * @param string  $message Message being written.
	 * @return void
	 */
	public static function add_user_note(
		int $post_id,
		int $user_id,
		string $message
	) {
		self::add_note( $post_id, $user_id, $message );
	}


	/**
	 * Adds a system note to a given post.
	 *
	 * @param integer $post_id The post ID.
	 * @param string  $message Message being written.
	 * @return void
	 */
	public static function add_system_note(
		int $post_id,
		string $message
	) {
		self::add_note( $post_id, 0, $message );
	}

	/**
	 * Inserts a note.
	 *
	 * @param integer $post_id The post ID.
	 * @param integer $user_id The user ID, or zero for system.
	 * @param string  $message The message.
	 * @return void
	 */
	protected static function add_note(
		int $post_id,
		int $user_id,
		string $message
	) {
		$user = get_user_by( 'id', $user_id );
		if ( $user ) {
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
			$comment_type         = self::COMMENT_USER_NOTE;
		} else {
			$comment_author       = __( 'System', 'event-vetting' );
			$comment_author_email = get_bloginfo( 'admin_email' );
			$comment_type         = self::COMMENT_SYSTEM_NOTE;
		}
		$result = wp_insert_comment( [
			'comment_author'       => $comment_author,
			'comment_author_email' => $comment_author_email,
			'comment_author_url'   => '',
			'comment_agent'        => 'Event Vetting',
			'comment_parent'       => 0,
			'comment_approved'     => 1,
			'comment_post_ID'      => $post_id,
			'comment_content'      => $message,
			'comment_type'         => $comment_type,
		] );
	}
}
