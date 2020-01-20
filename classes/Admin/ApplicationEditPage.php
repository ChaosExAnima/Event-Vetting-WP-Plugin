<?php
/**
 * Applications edit page.
 *
 * This page manipulates the Application post edit screen.
 *
 * @package Event-Vetting
 */

namespace EventVetting\Admin;

use EventVetting\Application;
use EventVetting\Bases\AdminPage;
use EventVetting\Notes;
use EventVetting\Roles;
use WP_Post;

class ApplicationEditPage extends AdminPage {
	/**
	 * Adds hooks for admin area.
	 *
	 * @return void
	 */
	public function admin_init() : void {
		add_action( 'post_action_event_vetting_vote', [ $this, 'action_vote' ] );
		add_action( 'post_action_event_vetting_admin_reset', [ $this, 'action_admin_reset' ] );
		add_action( 'current_screen', [ $this, 'on_screen' ] );

		add_filter( 'post_updated_messages', [ $this, 'filter_post_messages' ] );
	}

	/**
	 * Handles saving a post.
	 *
	 * @param integer $post_id The saved application ID.
	 * @return void
	 */
	public function action_vote( int $post_id ) : void {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( ! empty( $_REQUEST['vote'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$vote    = sanitize_text_field( wp_unslash( $_REQUEST['vote'] ) );
			$user_id = get_current_user_id();

			$result = Application::submit_vote( $user_id, $post_id, $vote );
			if ( is_wp_error( $result ) ) {
				// Using WP_Error instead of strings.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_die( $result );
			}
		}

		$location = add_query_arg( 'message', 20, get_edit_post_link( $post_id, 'url' ) );
		wp_safe_redirect( $location );
		exit;
	}

	/**
	 * Handles resetting an application to pending status.
	 *
	 * @param integer $post_id The saved application ID.
	 * @return void
	 */
	public function action_admin_reset( int $post_id ) : void {
		$result = Application::reset( $post_id );
		if ( is_wp_error( $result ) ) {
			// Using WP_Error instead of strings.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			wp_die( $result );
		}
		$location = add_query_arg( 'message', 21, get_edit_post_link( $post_id, 'url' ) );
		wp_safe_redirect( $location );
		exit;
	}

	/**
	 * Runs commands when on the current screen.
	 *
	 * @return void
	 */
	public function on_screen() : void {
		$current_screen = get_current_screen();
		if ( 'post' !== $current_screen->base || Application::POST_TYPE !== $current_screen->post_type ) {
			return;
		}

		add_action( 'add_meta_boxes', [ $this, 'action_meta_boxes' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'action_scripts' ] );
	}

	/**
	 * Registers custom meta boxes.
	 *
	 * @return void
	 */
	public function action_meta_boxes() : void {
		// Removes the publish metabox.
		remove_meta_box( 'submitdiv', null, 'side' );

		// Application details.
		add_meta_box(
			'event-vetting-application-details',
			__( 'Application Details', 'event-vetting' ),
			[ $this, 'render_details_meta_box' ],
			null,
			'normal',
			'high'
		);

		// Approval metabox.
		add_meta_box(
			'event-vetting-application-approval',
			__( 'Review', 'event-vetting' ),
			[ $this, 'render_approval_meta_box' ],
			null,
			'side',
			'high'
		);

		// Notes metabox.
		add_meta_box(
			'event-vetting-application-notes',
			__( 'Notes', 'event-vetting' ),
			[ $this, 'render_notes_meta_box' ],
			null,
			'side'
		);

		// Renames featured image.
		remove_meta_box( 'postimagediv', null, 'side' );
		add_meta_box(
			'postimagediv',
			__( 'Applicant Photo', 'event-vetting' ),
			'post_thumbnail_meta_box',
			null,
			'side',
			'low'
		);
	}

	/**
	 * Enqueues scripts for this page.
	 *
	 * @return void
	 */
	public function action_scripts() : void {
		wp_enqueue_style(
			EVENT_VETTING_PREFIX . 'admin_edit_styles',
			EVENT_VETTING_ASSETS . '/css/meta-boxes.css',
			[],
			EVENT_VETTING_VERSION
		);

		Notes::enqueue_js();
	}

	/**
	 * Renders the application details meta box.
	 *
	 * @param WP_Post $post The post instance.
	 * @return void
	 */
	public function render_details_meta_box( WP_Post $post ) : void {
		$details = get_post_meta( $post->ID, 'event_vetting_application_data', true );
		printf( '<table class="app-details">
			<thead>
				<tr>
					<th class="question">%s</th>
					<th class="answer">%s</th>
				</tr>
			</thead>
			<tbody>',
			esc_html__( 'Question', 'event-vetting' ),
			esc_html__( 'Answer', 'event-vetting' )
		);
		$i            = 0;
		$allowed_tags = [
			'a' => [
				'href'   => [],
				'target' => [],
			],
		];
		foreach ( $details as $field => $raw_answer ) {
			$answer = trim( $raw_answer );
			printf(
				'<tr class="%3$s"><td class="question">%1$s</td><td>%2$s</td></tr>',
				esc_html( $field ),
				wp_kses( make_clickable( $answer ), $allowed_tags ),
				esc_attr( $i % 2 ? 'alternate' : '' )
			);
			$i++;
		}
		echo '</tbody></table>';
	}

	/**
	 * Renders the approval metabox.
	 *
	 * @param WP_Post $post The post instance.
	 * @return void
	 */
	public function render_approval_meta_box( WP_Post $post ) : void {
		if ( Application::STATUS_APPROVED === $post->post_status ) {
			printf(
				'<p>%s</p>',
				esc_html__( 'This application has been approved', 'event-vetting' )
			);
		} elseif ( Application::STATUS_DENIED === $post->post_status ) {
			printf(
				'<p>%s</p>',
				esc_html__( 'This application has been denied', 'event-vetting' )
			);
		}

		if ( Application::STATUS_PENDING !== $post->post_status ) {
			if ( current_user_can( 'administrator' ) ) {
				$reset_link = add_query_arg(
					'action',
					'event_vetting_admin_reset',
					get_edit_post_link( $post )
				);
				printf(
					'<p><a href="%1$s">%2$s</a></p>',
					esc_url( $reset_link ),
					esc_html__( 'Admin override: Reset to pending', 'event-vetting' )
				);
			}
			return;
		}

		if ( ! current_user_can( Roles::VETTER_CAP ) ) {
			printf(
				'<p>%s</p>',
				esc_html__( 'This application is pending approval', 'event-vetting' )
			);
			return;
		}

		$votes        = Application::get_votes( $post->ID );
		$vote_options = Application::get_voting_options();
		$current_vote = 'yes';
		if ( isset( $votes[ get_current_user_id() ] ) ) {
			$current_vote = $votes[ get_current_user_id() ];
		}

		echo '<input name="action" value="event_vetting_vote" type="hidden" />';
		echo '<fieldset>';
		foreach ( $vote_options as $key => $text ) {
			printf(
				'<p><label>
					<input type="radio" name="vote" value="%1$s" %3$s />
					<span>%2$s</span>
				</label></p>',
				esc_attr( $key ),
				esc_html( $text ),
				checked( $key, $current_vote, false )
			);
		}
		echo '</fieldset>';
		submit_button(
			__( 'Submit Vote', 'event-vetting' ),
			'primary',
			'submit',
			false
		);
		echo '</form>';
	}

	/**
	 * Renders the notes metabox.
	 *
	 * @param WP_Post $post Current application instance.
	 * @return void
	 */
	public function render_notes_meta_box( WP_Post $post ) : void {
		echo '<ul class="notes">';
		$notes = Notes::get_notes_for_application( $post->ID );
		/**
		 * Iterate over notes.
		 *
		 * @var \WP_Comment
		 */
		foreach ( $notes as $note ) {
			$classes = [ 'note' ];
			if ( Notes::COMMENT_USER_NOTE === $note->comment_type ) {
				$classes[] = 'user';
			} elseif ( Notes::COMMENT_SYSTEM_NOTE === $note->comment_type ) {
				$classes[] = 'system';
			}

			$date_ts = strtotime( $note->comment_date_gmt );
			if ( time() - $date_ts < DAY_IN_SECONDS ) {
				$date_str = human_time_diff( strtotime( $note->comment_date_gmt ) ) . __( ' ago', 'event-vetting' );
			} else {
				$date_str = wp_date( 'M j, Y \a\t g:i a', $date_ts );
			}

			printf(
				'<li rel="%1$d" class="%2$s">
					<div class="note_content">%3$s</div>
					<p class="meta">
						<abbr class="exact-date">%4$s</abbr>
						<span class="author">%5$s</span>
					</p>
				</li>',
				intval( $note->comment_ID ),
				esc_attr( implode( ' ', $classes ) ),
				wp_kses_post( wpautop( wptexturize( make_clickable( $note->comment_content ) ) ) ),
				esc_html( $date_str ),
				esc_html( sprintf( __( 'by %s', 'event-vetting' ), $note->comment_author ) )
			);
		}
		if ( ! count( $notes ) ) {
			printf(
				'<li class="no-note">%s</li>',
				esc_html__( 'No notes yet.', 'event-vetting' )
			);
		}
		echo '</ul>';

		echo '<div class="add-note" id="js-add-note">';
		printf(
			'<p>
				<label for="add_order_note">%s</label>
				<textarea type="text" name="order_note" id="add_order_note" class="input-text large-text" cols="20" rows="5"></textarea>
			</p>',
			esc_html__( 'Add Note', 'event-vetting' )
		);
		printf(
			'<p>
				<button type="button" class="add_note button button-primary" id="js-add-note-button">%s</button>
				<span class="spinner" id="js-add-note-spinner"></span>
			</p>',
			esc_html__( 'Add Note', 'event-vetting' )
		);
		echo '</div>';
	}

	/**
	 * Filters the messages for the application post type.
	 *
	 * @param array $messages Array of messages, keyed by post type.
	 * @return array
	 */
	public function filter_post_messages( array $messages ) : array {
		$messages[ Application::POST_TYPE ] = [
			20 => __( 'Vote submitted.', 'event-vetting' ),
			21 => __( 'Application reset to pending.', 'event-vetting' ),
		];
		return $messages;
	}
}
