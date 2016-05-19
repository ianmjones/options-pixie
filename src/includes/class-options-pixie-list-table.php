<?php
/**
 * The main list table for displaying options.
 *
 * @link       https://www.bytepixie.com/options-pixie/
 * @since      1.0
 *
 * @package    Options_Pixie
 * @subpackage Options_Pixie/includes
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * The main list table for displaying options.
 *
 * @since      1.0
 * @package    Options_Pixie
 * @subpackage Options_Pixie/includes
 * @author     Byte Pixie <hello@bytepixie.com>
 */
class Options_Pixie_List_Table extends WP_List_Table {

	/**
	 * The Screen ID of the admin page.
	 *
	 * @access private
	 * @var string $page_hook The Screen ID of the admin page.
	 */
	private $page_hook;

	function __construct( $page_hook ) {
		$this->page_hook = $page_hook;

		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'option', // Singular name of the listed records.
				'plural'   => 'options', // Plural name of the listed records.
				'ajax'     => true, // Does this table support ajax?
			)
		);
		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 *
	 * @since 1.0
	 *
	 * @param string $which Is the markup for after (bottom) or before (top) the list.
	 */
	public function extra_tablenav( $which ) {
		$output = '';

		$output = apply_filters( 'options_pixie_extra_tablenav', $output, $which );

		echo $output;
	}

	/**
	 * Returns the name of the default column to show when list table collapsed to single column.
	 */
	public function list_table_primary_column( $default, $page_hook ) {
		if ( $page_hook === $this->page_hook ) {
			$default = 'option_name';
		}

		return $default;
	}

	/**
	 * When a column isn't explicitly handled by its own function, handle it here.
	 *
	 * @since 1.0
	 *
	 * @param array  $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 *
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'option_name':
			case 'option_value':
			case 'option_id':
			case 'autoload':
				return esc_attr( $item->$column_name );
				break;
			case 'type':
				return '';
				break;
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @since 1.0
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 */
	public function column_cb( $item ) {
		$output       = '';
		$bulk_actions = $this->get_bulk_actions();

		if ( ! empty( $bulk_actions ) ) {
			$output = sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				$this->_args['singular'],
				$item->option_id
			);
		}

		return $output;
	}

	/**
	 * Provides contents for each item's option_id.
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @since 1.0
	 *
	 * @param array $item A singular item (one full row's worth of data).
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	public function column_option_id( $item ) {
		$output      = apply_filters( 'options_pixie_column_display', $item->option_id, $item, array( 'column' => 'option_id' ) );
		$row_actions = apply_filters( 'options_pixie_column_row_actions', array(), $item, array( 'column' => 'option_id' ) );
		$row_actions = apply_filters( 'options_pixie_format_row_actions', $row_actions );

		return $output . $row_actions;
	}

	/**
	 * Provides contents for each item's option_name.
	 *
	 * @since 1.0
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data).
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	public function column_option_name( $item ) {
		$output      = apply_filters( 'options_pixie_column_display', $item->option_name, $item, array( 'column' => 'option_name' ) );
		$row_actions = apply_filters( 'options_pixie_column_row_actions', array(), $item, array( 'column' => 'option_name' ) );
		$row_actions = apply_filters( 'options_pixie_format_row_actions', $row_actions );

		return $output . $row_actions;
	}

	/**
	 * Provides contents for each item's option_value.
	 *
	 * @since 1.0
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data).
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	public function column_option_value( $item ) {
		$output      = apply_filters( 'options_pixie_column_display', $item->option_value, $item, array( 'column' => 'option_value' ) );
		$row_actions = apply_filters( 'options_pixie_column_row_actions', array(), $item, array( 'column' => 'option_value' ) );
		$row_actions = apply_filters( 'options_pixie_format_row_actions', $row_actions );

		return $output . $row_actions;
	}

	/**
	 * Provides contents for each item's autoload.
	 *
	 * @since 1.0
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data).
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	public function column_autoload( $item ) {
		$output      = apply_filters( 'options_pixie_column_display', $item->autoload, $item, array( 'column' => 'autoload' ) );
		$row_actions = apply_filters( 'options_pixie_column_row_actions', array(), $item, array( 'column' => 'autoload' ) );
		$row_actions = apply_filters( 'options_pixie_format_row_actions', $row_actions );

		return $output . $row_actions;
	}

	/**
	 * Provides contents for each item's type column.
	 *
	 * @since 1.0
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data).
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	public function column_type( $item ) {
		// This is a derived column based on the contents of the option_value.
		$value       = Options_Pixie_Data_Format::get_data_types( $item->option_value );
		$output      = apply_filters( 'options_pixie_column_display', $value, $item, array( 'column' => 'type' ) );
		$row_actions = apply_filters( 'options_pixie_column_row_actions', array(), $item, array( 'column' => 'type' ) );
		$row_actions = apply_filters( 'options_pixie_format_row_actions', $row_actions );

		return $output . $row_actions;
	}

	/**
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see   WP_List_Table::::single_row_columns()
	 *
	 * @since 1.0
	 *
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	public function get_columns() {
		$columns      = array();
		$bulk_actions = $this->get_bulk_actions();

		if ( ! empty( $bulk_actions ) ) {
			$columns['cb'] = '<input type="checkbox" />'; // Render a checkbox instead of text.
		}

		$columns['option_name']  = __( 'Option Name', 'options-pixie' );
		$columns['option_value'] = __( 'Option Value', 'options-pixie' );
		$columns['type']         = __( 'Type', 'options-pixie' );
		$columns['option_id']    = __( 'Option ID', 'options-pixie' );
		$columns['autoload']     = __( 'Auto Load', 'options-pixie' );

		return $columns;
	}

	/**
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @since 1.0
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 */
	public function get_sortable_columns() {
		$default_sort = false;

		if ( empty( $_REQUEST['orderby'] ) ) {
			$default_sort = true;
		}
		$sortable_columns = array(
			'option_name'  => array( 'option_name', $default_sort ), // true means it's already sorted.
			'option_value' => array( 'option_value', false ),
			'option_id'    => array( 'option_id', false ),
			'autoload'     => array( 'autoload', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * @since 1.0
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	public function get_bulk_actions() {
		static $bulk_actions = null;

		if ( null === $bulk_actions ) {
			$bulk_actions = apply_filters( 'options_pixie_get_bulk_actions', array() );
		}

		return $bulk_actions;
	}

	/**
	 * Returns an array of links to be used for switching views.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_views() {
		if ( empty( $_REQUEST['record_type'] ) || ! in_array( $_REQUEST['record_type'], array( 'all', 'permanent', 'transient' ) ) ) {
			$record_type = 'all';
		} else {
			$record_type = sanitize_key( $_REQUEST['record_type'] );
		}

		$blog_id = empty( $_REQUEST['blog_id'] ) ? '' : sanitize_key( $_REQUEST['blog_id'] );
		$search  = empty( $_REQUEST['s'] ) ? '' : sanitize_text_field( $_REQUEST['s'] );

		// Base required options used to build query args.
		$options = array(
			'blog_id' => $blog_id,
			's'       => $search,
		);

		//
		// All link.
		//
		$options['record_type'] = 'all';
		$all_count              = apply_filters( 'options_pixie_get_count', null, $options );

		$class = '';
		if ( 'all' == $record_type ) {
			$class = ' class="current"';
		}

		$type_links['all'] = "<a href='" . esc_url( add_query_arg( $options, $_SERVER['REQUEST_URI'] ) ) . "'$class>" . sprintf(
				_nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $all_count, 'records' ),
				number_format_i18n( $all_count )
			) . '</a>';

		//
		// Permanent link.
		//
		$options['record_type'] = 'permanent';
		$permanent_count        = apply_filters( 'options_pixie_get_count', null, $options );

		$class = '';
		if ( 'permanent' == $record_type ) {
			$class = ' class="current"';
		}

		$type_links['permanent'] = "<a href='" . esc_url( add_query_arg( $options, $_SERVER['REQUEST_URI'] ) ) . "'$class>" . sprintf(
				_nx( 'Permanent <span class="count">(%s)</span>', 'Permanent <span class="count">(%s)</span>', $permanent_count, 'records' ),
				number_format_i18n( $permanent_count )
			) . '</a>';

		//
		// Transient link.
		//
		$options['record_type'] = 'transient';
		$transient_count        = apply_filters( 'options_pixie_get_count', null, $options );

		$class = '';
		if ( 'transient' == $record_type ) {
			$class = ' class="current"';
		}

		$type_links['transient'] = "<a href='" . esc_url( add_query_arg( $options, $_SERVER['REQUEST_URI'] ) ) . "'$class>" . sprintf(
				_nx( 'Transient <span class="count">(%s)</span>', 'Transient <span class="count">(%s)</span>', $transient_count, 'records' ),
				number_format_i18n( $transient_count )
			) . '</a>';

		return $type_links;
	}

	/**
	 * Handles row and bulk action requests.
	 *
	 * @see   $this->prepare_items()
	 *
	 * @since 1.0
	 */
	public function process_action() {
		$action = $this->current_action();

		$ids = array();
		if ( isset( $_REQUEST[ $this->_args['singular'] ] ) && ! empty( $_REQUEST[ $this->_args['singular'] ] ) ) {
			$ids = $_REQUEST[ $this->_args['singular'] ];
		}

		$redirect = false;

		if ( ! empty( $action ) && ! empty( $ids ) ) {
			$blog_id  = empty( $_REQUEST['blog_id'] ) ? '' : sanitize_key( $_REQUEST['blog_id'] );
			$redirect = apply_filters( 'options_pixie_process_action', $redirect, $action, $ids, $blog_id );
		}

		if ( $redirect ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg( $this->_args['singular'], $_SERVER['REQUEST_URI'] );
			$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'action', 'action2' ), $_SERVER['REQUEST_URI'] );
			wp_redirect( $_SERVER['REQUEST_URI'] );
		}
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 *
	 * @since 1.0
	 */
	public function prepare_items() {
		global $wpdb, $_wp_column_headers, $mode;

		$user   = get_current_user_id();
		$screen = get_current_screen();

		$verified = false;
		if ( ! empty( $_REQUEST['_options_pixie_nonce'] ) && wp_verify_nonce( $_REQUEST['_options_pixie_nonce'], 'options-pixie-nonce' ) ) {
			$verified = true;
		}

		// Register the Columns.
		$columns               = $this->get_columns();
		$hidden                = get_hidden_columns( $screen );
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Get the user's saved options when no parameters given (clean page load).
		// Because WP_List_Table is very reliant on $_GET we can't do much with this though and must redirect.
		$retrieved_options = false;
		$remember_search   = true;
		if ( ! $verified ) {
			$options         = get_user_option( 'options_pixie_options' );
			$remember_search = isset( $options['remember_search'] ) ? $options['remember_search'] : true;

			if ( false !== $options && $remember_search ) {
				$retrieved_options = true;

				// We can make sure $mode global is up to date.
				$mode = empty( $options['mode'] ) ? 'list' : $options['mode'];
			}
		}

		if ( false === $retrieved_options ) {
			$blog_id     = empty( $_REQUEST['blog_id'] ) ? '' : sanitize_key( $_REQUEST['blog_id'] );
			$search      = empty( $_REQUEST['s'] ) ? '' : sanitize_text_field( $_REQUEST['s'] );
			$record_type = empty( $_REQUEST['record_type'] ) ? '' : sanitize_key( $_REQUEST['record_type'] );
			$orderby     = empty( $_REQUEST['orderby'] ) ? '' : sanitize_key( $_REQUEST['orderby'] );
			$order       = empty( $_REQUEST['order'] ) ? '' : sanitize_key( $_REQUEST['order'] );
			$mode        = ( ! empty( $_REQUEST['mode'] ) && 'excerpt' == $_REQUEST['mode'] ) ? 'excerpt' : 'list';

			$options = array(
				'blog_id'         => $blog_id,
				's'               => $search,
				'record_type'     => $record_type,
				'orderby'         => $orderby,
				'order'           => $order,
				'mode'            => $mode,
				'remember_search' => $remember_search,
			);
		}

		// Default the record ordering if not set.
		$options['orderby'] = empty( $options['orderby'] ) ? 'option_name' : $options['orderby'];
		$options['order']   = empty( $options['order'] ) ? 'asc' : $options['order'];

		// Save the user's selected options so they get them when they return.
		update_user_option( $user, 'options_pixie_options', $options );

		// Update the current URI with the new options.
		$redirect               = false;
		$orig_request_uri       = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( $options, $_SERVER['REQUEST_URI'] );

		if ( $_SERVER['REQUEST_URI'] !== $orig_request_uri ) {
			$redirect = true;
		}

		// Add nonce to URL.
		$nonce                  = wp_create_nonce( 'options-pixie-nonce' );
		$_SERVER['REQUEST_URI'] = add_query_arg( '_options_pixie_nonce', $nonce, $_SERVER['REQUEST_URI'] );

		// If we didn't get a nonce value redirect so that it is set and WP_List_Table's reliance on $_GET is satisfied.
		if ( $redirect ) {
			wp_redirect( $_SERVER['REQUEST_URI'] );
		}

		// Process the row or bulk action before doing any queries etc.
		if ( $verified ) {
			$this->process_action();
		}

		// Build the query from parameters.
		$query       = apply_filters( 'options_pixie_get_query_string', '', $options );
		$total_items = $wpdb->query( $query ); // return the total number of affected rows.

		// How many to display per page?
		$per_page_option = $screen->get_option( 'per_page', 'option' );
		$per_page        = get_user_meta( $user, $per_page_option, true );

		// If per_page option not set, use our default.
		if ( empty( $per_page ) || 1 > $per_page ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		// If we could not get our default something is wrong, use 5 instead.
		if ( empty( $per_page ) || 1 > $per_page ) {
			$per_page = 5;
		}

		// Which page is this?
		$current_page = $this->get_pagenum();

		// How many pages do we have in total?
		$total_pages = ceil( $total_items / $per_page );

		// If the current page is now too high select the last page.
		if ( $current_page > $total_pages ) {
			$current_page = $total_pages;
		}

		// Adjust the query to take pagination into account.
		if ( ! empty( $current_page ) && ! empty( $per_page ) ) {
			$offset = ( $current_page - 1 ) * $per_page;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $per_page;
		}

		// Register the pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			)
		);

		// Fetch the items.
		$this->items = $wpdb->get_results( $query );
	}

	/**
	 * Override pagination to add view_switcher.
	 *
	 * @since 1.0
	 *
	 * @param string $which
	 */
	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which ) {
			$this->view_switcher( $mode );
		}
	}

	/**
	 * Generate row actions div.
	 *
	 * This function is WP_List_Table::row_actions made static (as it should be).
	 *
	 * @since 1.0
	 *
	 * @param array $actions        The list of actions
	 * @param bool  $always_visible Whether the actions should be always visible
	 *
	 * @return string
	 */
	private static function _row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i            = 0;

		if ( ! $action_count ) {
			return '';
		}

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		return $out;
	}

	/**
	 * Handler for the options_pixie_format_row_actions action.
	 *
	 * @since 1.0
	 *
	 * @param array|string $actions
	 *
	 * @return string
	 */
	public static function format_row_actions( $actions ) {
		// If we have been called already and processed whatever was given, just return it again.
		if ( is_string( $actions ) ) {
			return $actions;
		}

		return Options_Pixie_List_Table::_row_actions( $actions );
	}
}