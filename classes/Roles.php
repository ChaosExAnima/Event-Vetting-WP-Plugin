<?php
/**
 * Handler for managing roles.
 *
 * @package Event-Vetting
 */

namespace EventVetting;

class Roles {
	/**
	 * Name of new role.
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
	 * Adds role if it doesn't exist already.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! get_role( self::VETTER_ROLE ) ) {
			$subscriber = get_role( 'subscriber' );
			add_role(
				self::VETTER_ROLE,
				__( 'Vetter', 'event-vetting' ),
				array_merge( $subscriber->capabilities, [
					self::VETTER_CAP       => true,
					'view_admin_dashboard' => true,
				] )
			);
		}

		$admin = get_role( 'administrator' );
		$admin->add_cap( self::VETTER_CAP );
	}
}
