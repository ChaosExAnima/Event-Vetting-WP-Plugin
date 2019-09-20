<?php
/**
 * Class to handle registering an admin page.
 *
 * @package Event-Vetting
 */

namespace EventVetting\Admin;

class PageFactory {

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	protected $menu_slug;

	/**
	 * Hook slug.
	 *
	 * @var string
	 */
	protected $hook;

	/**
	 * The page's capability to view.
	 *
	 * Used as a default for children.
	 *
	 * @var string
	 */
	protected $capability;

	/**
	 * Constructor.
	 *
	 * @param string $menu_slug The menu slug.
	 */
	public function __construct( string $menu_slug ) {
		$this->menu_slug = $menu_slug;
	}

	/**
	 * Initializes the menu.
	 *
	 * @param string   $page_title      Title of the page.
	 * @param string   $menu_title      Optional. Menu title.
	 * @param callable $render_callback Callback to render page HTML.
	 * @param string   $capability      Capability required. Defaults to manage_options.
	 * @param string   $icon            Page icon.
	 * @param integer  $position        Position of page in menu.
	 */
	public function menu_init(
		string $page_title,
		string $menu_title = '',
		callable $render_callback,
		string $capability = 'manage_options',
		string $icon = '',
		int $position = 10
	) {
		if ( $this->hook ) {
			return;
		}
		if ( ! $menu_title ) {
			$menu_title = $page_title;
		}
		$this->capability = $capability;

		$this->hook = add_menu_page(
			$page_title,
			$menu_title,
			$capability,
			$this->menu_slug,
			$render_callback,
			$icon,
			$position
		);
	}

	/**
	 * Adds a submenu page.
	 *
	 * @param string   $page_title      Submenu page title.
	 * @param callable $render_callback Callback to render page HTML.
	 * @param string   $menu_slug       Optional menu slug.
	 * @param string   $menu_title      The menu title of the submenu. Defaults to page title.
	 * @param string   $capability      Capability to view submenu page. Defaults to parent capability.
	 * @return string
	 */
	public function add_child(
		string $page_title,
		callable $render_callback,
		string $menu_slug = '',
		string $menu_title = '',
		string $capability = ''
	) : string {
		if ( ! $capability ) {
			$capability = $this->capability;
		}
		if ( ! $menu_title ) {
			$menu_title = $page_title;
		}
		if ( ! $menu_slug ) {
			$menu_slug = $this->menu_slug . '_' . sanitize_title( $page_title );
		}
		return (string) add_submenu_page(
			$this->menu_slug,
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$render_callback
		);
	}

	/**
	 * Gets the page hook.
	 *
	 * @return string
	 */
	public function get_hook() : string {
		return $this->hook;
	}
}
