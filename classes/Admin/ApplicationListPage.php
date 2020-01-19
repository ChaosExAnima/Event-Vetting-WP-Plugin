<?php
/**
 * Applications list page.
 *
 * This page manipulates the Application post table.
 *
 * @package Event-Vetting
 */

namespace EventVetting\Admin;

use EventVetting\Application;
use EventVetting\Bases\AdminPage;
use WP_Query;

class ApplicationListPage extends AdminPage {
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
		if ( 'edit' !== $current_screen->base || Application::POST_TYPE !== $current_screen->post_type ) {
			return;
		}

		// Sets pending to default status.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_REQUEST['post_status'] ) ) {
			$_REQUEST['post_status'] = Application::STATUS_PENDING;
		}

		// Sets default status to pending.
		add_filter( 'pre_get_posts', function( WP_Query $wp_query ) {
			if ( $wp_query->is_main_query() && ! $wp_query->get( 'post_status' ) ) {
				$wp_query->set( 'post_status', Application::STATUS_PENDING );
			}
		} );

		// Removes "all" view.
		add_filter( 'views_edit-' . Application::POST_TYPE, [ $this, 'filter_status_views' ] );
	}

	/**
	 * Filters the current views to remove all and show current user.
	 *
	 * @param array $views The views for the posts table.
	 * @return array
	 */
	public function filter_status_views( array $views ) : array {
		unset( $views['all'] );
		return $views;
	}
}
