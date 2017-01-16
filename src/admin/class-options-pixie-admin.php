<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://www.bytepixie.com/options-pixie/
 * @since      1.0
 *
 * @package    Options_Pixie
 * @subpackage Options_Pixie/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    Options_Pixie
 * @subpackage Options_Pixie/admin
 * @author     Byte Pixie <hello@bytepixie.com>
 */
class Options_Pixie_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var string $options_pixie The ID of this plugin.
	 */
	private $options_pixie;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The Screen ID of the admin page.
	 *
	 * @since  1.0
	 * @access private
	 * @var string $page_hook The Screen ID of the admin page.
	 */
	private $page_hook;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 *
	 * @param string $options_pixie The name of this plugin.
	 * @param string $version       The version of this plugin.
	 */
	public function __construct( $options_pixie, $version ) {

		$this->options_pixie = $options_pixie;
		$this->version       = $version;
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {
		if ( ! self::our_screen( get_current_screen(), $this->page_hook ) ) {
			return;
		}

		wp_enqueue_style( $this->options_pixie,
			plugin_dir_url( __FILE__ ) . 'css/options-pixie-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		if ( ! self::our_screen( get_current_screen(), $this->page_hook ) ) {
			return;
		}

		wp_enqueue_script( $this->options_pixie,
			plugin_dir_url( __FILE__ ) . 'js/options-pixie-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Because we might re-direct we need to ensure no output has gone out yet.
	 *
	 * @since  1.0
	 */
	public function delay_output() {
		ob_start();
	}

	/**
	 * Add admin menu items.
	 *
	 * @since 1.0
	 */
	public function add_menu_items() {
		$admin_title = apply_filters( 'options_pixie_menu_title', __( 'Options Pixie', 'options-pixie' ) );

		if ( is_multisite() ) {
			$page_hook = add_submenu_page(
				'settings.php',
				$admin_title,
				$admin_title,
				'manage_network_options',
				'options-pixie',
				array( $this, 'display_admin_page' )
			);
		} else {
			$page_hook = add_options_page(
				$admin_title,
				$admin_title,
				'manage_options',
				'options-pixie',
				array( $this, 'display_admin_page' )
			);
		}

		// Because the loader does not know the page's given hook name until we've added the page to the menu.
		if ( false !== $page_hook ) {
			do_action( 'options_pixie_admin_page_hooked', $page_hook );
		}
	}

	/**
	 * The admin page has been hooked.
	 *
	 * @since 1.0
	 *
	 * @param bool|string $page_hook The admin page's hook name.
	 */
	public function admin_page_hooked( $page_hook ) {
		$this->page_hook = $page_hook;

		add_action( "load-{$this->page_hook}", array( $this, 'add_screen_options' ) );
		add_action( "load-{$this->page_hook}", array( $this, 'add_screen_help' ) );
		add_filter( 'screen_settings', array( $this, 'screen_settings' ), 10, 2 );

		if ( is_multisite() ) {
			add_filter( "manage_{$this->page_hook}-network_columns", array( $this, 'manage_screen_columns' ) );
		} else {
			add_filter( "manage_{$this->page_hook}_columns", array( $this, 'manage_screen_columns' ) );
		}
	}

	/**
	 * Add screen options.
	 *
	 * @since 1.0
	 */
	public function add_screen_options() {
		add_screen_option(
			'per_page',
			array(
				'label'   => _x( 'Records', 'Records to show per page (screen option).', 'options-pixie' ),
				'default' => 20,
				'option'  => 'options_pixie_records_per_page',
			)
		);
	}

	/**
	 * Add screen help.
	 *
	 * @since 1.0
	 */
	public function add_screen_help() {
		$screen = get_current_screen();

		$help_tabs[] = array(
			'id'      => 'help-columns',
			'title'   => __( 'Columns', 'options-pixie' ),
			'content' => __( '
					<h3>Columns</h3>
					<p>
						<dl>
							<dt>Option ID</dt>
							<dd>The unique ID given automatically to each options record.</dd>
							<dt>Option Name</dt>
							<dd>
							The unique name given to each options record, used by WordPress and third parties to get and set options.<br>
							If the Option Name begins with "_transient_" it is a temporary record that will be removed after a specific timeout period.<br>
							Option Names begining with "_transient_timeout_" specify the UNIX timestamp after which the record and its counterpart may be removed.
							</dd>
							<dt>Option Value</dt>
							<dd>The actual value for the record, can be any format that can be stored in a string.</dd>
							<dt>Type</dt>
							<dd>
							A column created by the plugin to show the type of data being stored in the Option Value. This column is not stored in the database.<br>
							When the type is blank the Option Value is general text or numeric data.<br>
							<strong>"S"</strong> is for Serialized data.<br>
							<strong>"J"</strong> is for JSON data.<br>
							<strong>"O"</strong> is for Object.<br>
							<strong>"b64"</strong> is for Base 64 encoded data. At present the plugin can only determine if data has been Base 64 encoded if the contained data is Serialized, JSON or an Object.<br>
							<strong>"!!!"</strong> is shown when the Serialized value is broken in some way, usually by string length indicators not matching the length of the string it partners.<br>
							This column can not be sorted as it is derived.
							</dd>
							<dt>Autoload</dt>
							<dd>Can be "yes" or "no". Determines whether the option should be automatically loaded into the object cache on every page load.</dd>
						</dl>
					</p>
					<p>
						The <strong>Option ID</strong>, <strong>Type</strong> and <strong>Autoload</strong> columns can be shown and hidden from the Screen Options panel.
					</p>
				',
				'options-pixie'
			),
		);
		$help_tabs[] = array(
			'id'      => 'help-search',
			'title'   => __( 'Search', 'options-pixie' ),
			'content' => __( '
					<h3>Search</h3>
					<p>
						You can search and filter the shown options records by entering text into the Search box on the top right of the table and using the "Search" button.<br>
						When you use the search box, the plugin will show all records that either have the same Option ID if numeric, or where the Option Name or Option Value contains the search text.<br>
					</p>
					<p>
						You can also use the "All", "Permanent" and "Transient" links to restrict the records being shown to those types of records.
					</p>
					<p>
						The number of records to show per page can be changed from the "Screen Options" panel. There you will find a "Records" box where you can change the number of records to show, use the "Apply" button to confirm the change.
					</p>
				',
				'options-pixie'
			),
		);
		$help_tabs[] = array(
			'id'      => 'help-rich-view',
			'title'   => __( 'Rich View', 'options-pixie' ),
			'content' => __( '
					<h3>Rich View</h3>
					<p>
						The default List View with icon <span class="dashicons list-view"></span> shows the Option Values in their plain text form.
					</p>
					<p>
						The Rich View with icon <span class="dashicons excerpt-view"></span> shows the more complex data in Option Values in an easier to understand manner.<br>
						When the data in the Option Value can be converted into an array of values the plugin will show the keys and values, and also expansion controls when there are multiple levels.
					</p>
				',
				'options-pixie'
			),
		);

		$help_tabs = apply_filters( 'options_pixie_set_help_tabs', $help_tabs );

		if ( ! empty( $help_tabs ) && is_array( $help_tabs ) ) {
			foreach ( $help_tabs as $help_tab ) {
				if ( ! empty( $help_tab['id'] ) && ! empty( $help_tab['title'] ) && ! empty( $help_tab['content'] ) ) {
					$screen->add_help_tab( $help_tab );
				}
			}
		}

		$help_sidebar = apply_filters( 'options_pixie_set_help_sidebar', '' );

		if ( ! empty( $help_sidebar ) ) {
			$screen->set_help_sidebar( $help_sidebar );
		}
	}

	/**
	 * Handles screen_settings filter to add screen settings to Screen Options panel.
	 *
	 * @since 1.0
	 *
	 * @param string    $screen_settings
	 * @param WP_Screen $screen
	 *
	 * @return string
	 */
	public function screen_settings( $screen_settings, $screen ) {
		// Only add our extra screen settings when on our screen.
		if ( empty( $this->page_hook ) || ! $this->our_screen( $screen, $this->page_hook ) ) {
			return $screen_settings;
		}

		$remember_search = true;
		$options         = get_user_option( 'options_pixie_options' );

		if ( false !== $options && isset( $options['remember_search'] ) && is_bool( $options['remember_search'] ) ) {
			$remember_search = $options['remember_search'];
		}

		$checked = $remember_search ? ' checked="checked"' : '';
		$screen_settings .= '<label for="remember-search">';
		$screen_settings .= '<input class="remember-search-tog" name="remember_search" type="checkbox" id="remember-search"' . $checked . '>';
		$screen_settings .= __( 'Remember Search & Sort', 'options-pixie' );
		$screen_settings .= '</label>';

		return $screen_settings;
	}

	/**
	 * Handle AJAX request to save state of Remember Search Screen option.
	 *
	 * @since  1.0
	 */
	public function ajax_toggle_remember_search() {
		$options = get_user_option( 'options_pixie_options' );

		$options['remember_search'] = ( 'false' == $_REQUEST['remember_search'] ) ? false : true;

		update_user_option( get_current_user_id(), 'options_pixie_options', $options );
		wp_die();
	}

	/**
	 * Handles the manage_$screen->id_columns filter to supply columns that can be shown or hidden via the screen options panel.
	 *
	 * @since 1.0
	 *
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function manage_screen_columns( $columns ) {
		$columns['type']      = __( 'Type', 'options-pixie' );
		$columns['option_id'] = __( 'Option ID', 'options-pixie' );
		$columns['autoload']  = __( 'Autoload', 'options-pixie' );

		return $columns;
	}

	/**
	 * Display the admin page.
	 *
	 * @since 1.0
	 */
	public function display_admin_page() {
		$options_pixie_list_table = new Options_Pixie_List_Table( $this->page_hook );
		$options_pixie_list_table->prepare_items();

		include plugin_dir_path( __FILE__ ) . 'partials/options-pixie-admin-display.php';
	}

	/**
	 * Let records_per_page option be set.
	 *
	 * @since 1.0
	 *
	 * @param string $status
	 * @param string $option The option name.
	 * @param string $value  The option value.
	 *
	 * @return string, The status or value.
	 */
	public function set_records_per_page_option( $status, $option, $value ) {
		if ( 'options_pixie_records_per_page' == $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Formats the option_value column for display.
	 *
	 * @since 1.0
	 *
	 * @param string $value
	 * @param object $item
	 * @param array  $options
	 *
	 * @return string|void
	 */
	public function column_option_value( $value, $item, $options ) {
		global $mode;

		$chars = 100;
		if ( 'list' == $mode && strlen( trim( $value ) ) > $chars ) {
			if ( ! isset( $options['collapsed'] ) || true === $options['collapsed'] ) {
				if ( is_serialized( $value ) ) {
					$boundary = ';';
				} elseif ( Options_Pixie_Data_Format::is_json( $value ) ) {
					$boundary = ',';
				} else {
					$boundary = ' ';
				}

				$truncated = $this->truncate_chars( $value, $chars, $boundary );

				if ( $truncated !== $value ) {
					$value = $truncated . ' &hellip;';
				}
			}
		} elseif ( 'excerpt' === $mode ) {
			$value = Options_Pixie_Data_Format::to_html( $value, 'options-pixie-rich-view' );
		}

		// Whether truncated or not, in list mode we're handling raw data that must be escaped.
		if ( 'list' == $mode ) {
			$value = esc_html( $value );
		}

		return $value;
	}

	/**
	 * Builds row actions for the option_value column.
	 *
	 * @since 1.0
	 *
	 * @param array  $actions
	 * @param object $item
	 * @param array  $options
	 *
	 * @return string|void
	 */
	public function column_option_value_row_actions( $actions, $item, $options ) {
		global $mode;

		$chars = 100;
		if ( 'list' == $mode && strlen( trim( $item->option_value ) ) > $chars ) {
			if ( ! isset( $options['collapsed'] ) || true === $options['collapsed'] ) {
				$value = $this->column_option_value( $item->option_value, $item, $options );

				if ( $item->option_value !== $value ) {
					$actions = array(
						'expand' => '<a href="#" class="truncate collapsed">' . _x( 'Expand', 'Show more data', 'options-pixie' ) . '</a>',
					);
				}
			} else {
				$actions = array(
					'collapse' => '<a href="#" class="truncate expanded">' . _x( 'Collapse', 'Show less data', 'options-pixie' ) . '</a>',
				);
			}
		} elseif ( 'excerpt' == $mode && Options_Pixie_Data_Format::is_expandable( $item->option_value ) ) {
			$actions = array(
				'expand_all'   => '<a href="#" class="expand-all">' . _x( 'Expand All', 'Show all collapsed array data', 'options-pixie' ) . '</a>',
				'collapse_all' => '<a href="#" class="collapse-all">' . _x( 'Collapse All', 'Collapse and hide all array data', 'options-pixie' ) . '</a>',
			);
		}

		return $actions;
	}

	/**
	 * Handler for options_pixie_column_display filter.
	 *
	 * @since 1.0
	 *
	 * @param mixed  $value
	 * @param object $item
	 * @param array  $options
	 *
	 * @return string
	 */
	public function column_display( $value, $item, $options = array() ) {
		if ( empty( $item ) || empty( $options['column'] ) ) {
			return $value;
		}

		switch ( $options['column'] ) {
			case 'option_value':
				$value = $this->column_option_value( $value, $item, $options );
				break;
			case 'type':
				$value = join( ' / ', $value );

				if ( false !== strpos( $value, '!!!' ) ) {
					$value = Options_Pixie_Data_Format::wrap_with_error( $value, __( 'Broken data', 'options-pixie' ) );
				}
				break;
		}

		// If the value to be displayed is the same as the raw data it must be escaped before display.
		if ( isset( $item->{$options['column']} ) &&
		     ! empty( $item->{$options['column']} ) &&
		     $value === $item->{$options['column']}
		) {
			$value = esc_html( $value );
		}

		return $value;
	}

	/**
	 * Handler for options_pixie_column_row_actions filter.
	 *
	 * @since 1.0
	 *
	 * @param mixed  $actions
	 * @param object $item
	 * @param array  $options
	 *
	 * @return string
	 */
	public function column_row_actions( $actions, $item, $options = array() ) {
		if ( empty( $item ) || empty( $options['column'] ) || ! isset( $item->{$options['column']} ) || empty( $item->{$options['column']} ) ) {
			return $actions;
		}

		switch ( $options['column'] ) {
			case 'option_value':
				$actions = $this->column_option_value_row_actions( $actions, $item, $options );
				break;
		}

		return $actions;
	}

	/**
	 * Handles AJAX request to toggle truncation of a column.
	 *
	 * @since 1.0
	 */
	public function ajax_toggle_truncate() {
		global $mode;
		$mode = ( ! empty( $_REQUEST['mode'] ) && 'excerpt' == $_REQUEST['mode'] ) ? 'excerpt' : 'list';

		$blog_id   = empty( $_REQUEST['blog_id'] ) ? '' : sanitize_key( $_REQUEST['blog_id'] );
		$option_id = sanitize_key( $_REQUEST['option_id'] );
		$column    = sanitize_key( $_REQUEST['column'] );
		$collapsed = sanitize_key( $_REQUEST['collapsed'] );

		$item = apply_filters( 'options_pixie_get_item', null, array( 'blog_id' => $blog_id, 'option_id' => $option_id ) );

		if ( ! empty( $item ) ) {
			$options['column']    = $column;
			$options['collapsed'] = ( 'true' === $collapsed ) ? false : true;
			$value                = apply_filters( 'options_pixie_column_display', $item->{$column}, $item, $options );
			$row_actions          = apply_filters( 'options_pixie_column_row_actions', $item->{$column}, $item, $options );
			$row_actions          = apply_filters( 'options_pixie_format_row_actions', $row_actions );
			echo $value . $row_actions;
		}
		wp_die();
	}

	/**
	 * Generate query string for given parameters.
	 *
	 * @since 1.0
	 *
	 * @param string $query
	 * @param array  $options
	 *
	 * @return string
	 */
	public function get_query_string( $query, $options = array() ) {
		global $wpdb;

		$blog_id     = empty( $options['blog_id'] ) ? '' : sanitize_key( $options['blog_id'] );
		$search      = empty( $options['s'] ) ? '' : sanitize_text_field( $options['s'] );
		$record_type = empty( $options['record_type'] ) ? 'all' : sanitize_key( $options['record_type'] );
		$orderby     = empty( $options['orderby'] ) ? '' : sanitize_key( $options['orderby'] );
		$order       = empty( $options['order'] ) ? 'asc' : sanitize_key( $options['order'] );

		// If a blog in a multisite has been selected, temporarily switch all queries to that blog.
		if ( is_numeric( $blog_id ) && is_multisite() ) {
			$blog_id = (int) $blog_id;
			switch_to_blog( $blog_id );
		}

		$query = 'SELECT * FROM ' . $wpdb->options;
		$query .= ' WHERE 1=1';

		$search_values = array();

		if ( ! empty( $search ) ) {
			$query .= " AND ( {$wpdb->options}.option_name LIKE %s";
			$search_values[] = "%{$search}%";
			$query .= " OR {$wpdb->options}.option_value LIKE %s";
			$search_values[] = "%{$search}%";

			if ( is_numeric( $search ) ) {
				$query .= " OR {$wpdb->options}.option_id = %d";
				$search_values[] = "{$search}";
			}
			$query .= ')';
		}

		if ( ! empty( $record_type ) && 'all' != $record_type ) {
			if ( 'transient' === $record_type ) {
				$query .= " AND (";
				$query .= " {$wpdb->options}.option_name LIKE %s";
				$query .= " OR {$wpdb->options}.option_name LIKE %s";
				$query .= " )";
			} else {
				$query .= " AND {$wpdb->options}.option_name NOT LIKE %s";
				$query .= " AND {$wpdb->options}.option_name NOT LIKE %s";
			}
			$search_values[] = '_transient%';
			$search_values[] = '_site_transient%';
		}

		// Parameters that are going to be used to order the result.
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$query .= " ORDER BY {$orderby} {$order}";
		}

		if ( ! empty( $search_values ) ) {
			$query = $wpdb->prepare( $query, $search_values );
		}

		// If a blog in a multisite has been selected, switch all queries back to previous blog.
		if ( is_numeric( $blog_id ) && is_multisite() ) {
			restore_current_blog();
		}

		return $query;
	}

	/**
	 * Returns a single item.
	 *
	 * @since 1.0
	 *
	 * @param object $item
	 * @param array  $options
	 *
	 * @return object
	 */
	public function get_item( $item, $options ) {
		global $wpdb;

		$blog_id     = empty( $options['blog_id'] ) ? '' : sanitize_key( $options['blog_id'] );
		$option_id   = empty( $options['option_id'] ) ? '' : sanitize_key( $options['option_id'] );
		$option_name = empty( $options['option_name'] ) ? '' : sanitize_key( $options['option_name'] );

		if ( is_numeric( $blog_id ) && is_multisite() ) {
			$blog_id = (int) $blog_id;
			switch_to_blog( $blog_id );
		}

		if ( ! empty( $option_id ) ) {
			$where[] = "{$wpdb->options}.option_id = %d";
			$prep[]  = $option_id;
		}

		if ( ! empty( $option_name ) ) {
			$where[] = "{$wpdb->options}.option_name = %s";
			$prep[]  = $option_name;
		}

		if ( ! empty( $where ) && ! empty( $prep ) ) {
			$query = 'SELECT * FROM ' . $wpdb->options;
			$query .= ' WHERE ' . implode( ' AND ', $where );

			$query = $wpdb->prepare( $query, $prep );
			$item  = $wpdb->get_row( $query );
		}

		if ( is_numeric( $blog_id ) && is_multisite() ) {
			restore_current_blog();
		}

		return $item;
	}

	/**
	 * Run query for given parameters and return count of effected records.
	 *
	 * @since 1.0
	 *
	 * @param string $query
	 * @param array  $options
	 *
	 * @return string
	 */
	public function get_count( $query, $options = array() ) {
		global $wpdb;

		$query = apply_filters( 'options_pixie_get_query_string', $query, $options );

		return $wpdb->query( $query );
	}

	/**
	 * Truncate a string to given number of characters, or less if boundary string found.
	 *
	 * @since 1.0
	 *
	 * @param string $text
	 * @param int    $chars
	 * @param string $boundary
	 *
	 * @return string
	 */
	function truncate_chars( $text, $chars = 100, $boundary = ';' ) {
		if ( strlen( $text ) > ceil( $chars * 1.2 ) ) {
			$text = substr( $text, 0, $chars );

			// Step back to a boundary if within 20% of max length.
			$boundary_pos = strrpos( $text, $boundary ) + 1;
			if ( ceil( $chars * 0.8 ) <= $boundary_pos ) {
				$text = substr( $text, 0, $boundary_pos );
			}
		}

		return $text;
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 *
	 * @since 1.0
	 *
	 * @param string $output The current output.
	 * @param string $which  Is the markup for after (bottom) or before (top) the list.
	 *
	 * @return string
	 */
	public function extra_tablenav( $output, $which ) {
		if ( $which == 'top' ) {
			// The html that goes before the table is appended to $output here.
			if ( is_multisite() ) {
				$output .= '<div class="alignleft actions options-pixie-extra-tablenav">';

				$current_blog_id = empty( $_REQUEST['blog_id'] ) ? '' : sanitize_key( $_REQUEST['blog_id'] );
				$output .= '<label for="blog-id-selector-top" class="screen-reader-text">' . __( 'Select Site', 'options-pixie' ) . '</label>';
				$output .= '<select name="blog_id" id="blog-id-selector-top" autocomplete="off">';
				$output .= '<option value="" disabled="disabled">&mdash; ' . _x( 'Site', 'Site to view records for', 'options-pixie' ) . ' &mdash;</option>';

				foreach ( $this->_get_sites( array( 'limit' => 0 ) ) as $blog ) {
					$blog_id     = empty( $blog['blog_id'] ) ? '' : $blog['blog_id'];
					$description = untrailingslashit( trim( $blog['domain'] ) . trim( $blog['path'] ) );

					$selected = '';
					if ( $current_blog_id == $blog_id ) {
						$selected = ' selected="selected"';
					}
					$output .= sprintf(
						'<option value="%1$s"' . $selected . '>%2$s</option>',
						esc_attr( $blog_id ),
						esc_html( $description )
					);
				}
				$output .= '</select>';
				$output .= '<input type="submit" name="" id="apply-blog-id-top" class="button action" value="' . __( 'Switch Site', 'options-pixie' ) . '">';
				$output .= '</div>';
			}
		}

		if ( $which == 'bottom' ) {
			// The html that goes after the table is appended to $output there.
		}

		return $output;
	}

	/**
	 * Is the given screen ours?
	 *
	 * @param WP_Screen $screen
	 * @param string    $page_hook
	 *
	 * @return bool
	 */
	public static function our_screen( $screen, $page_hook ) {
		if ( ! empty( $screen->id ) && ! empty( $page_hook ) && ( $screen->id === $page_hook || $screen->id === $page_hook . '-network' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Handler for the options_pixie_admin_page_footer filter.
	 *
	 * @param string $content Current footer content to be appended to, or replaced.
	 *
	 * @return string
	 */
	public function get_admin_page_footer( $content ) {
		$content .= '
			<div class="clear">
				<p>
					Like Options Pixie? You\'ll <strong>LOVE</strong> <a href="https://www.bytepixie.com/options-pixie-pro/" target="_blank">Options Pixie Pro</a>. <strong>Add</strong>, <strong>edit</strong>, <strong>delete</strong> and <strong>fix</strong> your WordPress site\'s options records with style.
					<a href="https://www.bytepixie.com/options-pixie-pro/" class="button button-primary regular" target="_blank">Buy Now!</a>
				</p>
			</div>
			';

		return $content;
	}

	/**
	 * Return an array of sites for a network or networks.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function _get_sites( $args ) {
		global $wp_version;

		$results = array();

		if ( version_compare( $wp_version, '4.6-dev', '<' ) ) {
			$results = wp_get_sites( $args );
		} else {
			$_sites = get_sites( $args );

			foreach ( $_sites as $_site ) {
				$_site     = get_site( $_site );
				$results[] = $_site->to_array();
			}
		}

		return $results;
	}
}
