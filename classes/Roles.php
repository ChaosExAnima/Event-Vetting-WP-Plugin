<?php
/**
 * Handler for managing roles.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Roles {
	/**
	 * Name of vetter role.
	 *
	 * @var string
	 */
	const VETTER_ROLE = 'event_vetter';

	/**
	 * Main vetting capability name.
	 *
	 * @var string
	 */
	const VETTER_CAP = 'review_event_applications';

	/**
	 * Name of safety role.
	 *
	 * @var string
	 */
	const SAFETY_ROLE = 'event_safety';

	/**
	 * Safety capability name.
	 *
	 * @var string
	 */
	const SAFETY_CAP = 'review_event_safety';

	/**
	 * Installs roles.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! get_role( self::VETTER_ROLE ) ) {
			$subscriber = get_role( 'subscriber' );
		$base_caps  = $subscriber->capabilities;

		// Adds capability to view dashboard if WC is installed.
		if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
			$base_caps['view_admin_dashboard'] = true;
		}

		delete_role( self::VETTER_ROLE );
			add_role(
				self::VETTER_ROLE,
			__( 'Event Vetter', 'event-vetting' ),
			array_merge( $base_caps, [
				self::VETTER_CAP => true,
			] )
		);

		delete_role( self::SAFETY_ROLE );
		add_role(
			self::SAFETY_ROLE,
			__( 'Event Safety', 'event-vetting' ),
			array_merge( $base_caps, [
				self::SAFETY_ROLE => true,
				] )
			);

		// Updates admin with all capabilities.
		$admin = get_role( 'administrator' );
		$admin->add_cap( self::VETTER_CAP );
		$admin->add_cap( self::SAFETY_CAP );
	}
}
