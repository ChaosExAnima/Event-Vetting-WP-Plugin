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
use WP_Post;

class ApplicationEditPage extends AdminPage {
	/**
	 * Adds hooks for admin area.
	 *
	 * @return void
	 */
	public function admin_init() {
		add_action( 'current_screen', [ $this, 'on_screen' ] );
	}

	/**
	 * Runs commands when on the current screen.
	 *
	 * @return void
	 */
	public function on_screen() {
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
	}

	/**
	 * Renders the application details meta box.
	 *
	 * @param WP_Post $post The post instance.
	 * @return void
	 */
	public function render_details_meta_box( WP_Post $post ) {
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
				'rel'    => [],
			],
		];
		foreach ( $details as $field => $raw_answer ) {
			$answer = trim( $raw_answer );
			if ( filter_var( $raw_answer, FILTER_VALIDATE_URL ) ) {
				$answer = sprintf(
					'<a href="%1$s" target="_blank" rel="nofollow">%1$s</a>',
					esc_url( $answer )
				);
			} elseif ( is_email( $answer ) ) {
				$answer = sprintf(
					'<a href="mailto:%1$s" target="_blank" rel="nofollow">%1$s</a>',
					esc_attr( sanitize_email( $answer ) )
				);
			}
			printf(
				'<tr class="%3$s"><td class="question">%1$s</td><td>%2$s</td></tr>',
				esc_html( $field ),
				wp_kses( $answer, $allowed_tags ),
				esc_attr( $i % 2 ? 'alternate' : '' )
			);
			$i++;
		}
		echo '</tbody></table>';
	}
}
