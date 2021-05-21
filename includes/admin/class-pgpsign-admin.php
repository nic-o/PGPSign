<?php
/**
 * Source: https://github.com/Veraxus/wp-list-table-example
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class PGPSign_List_Table extends WP_List_Table {

    /**
     * PGPSign_List_Table constructor
     */
    public function __construct () {
        // Set parent defaults.
        parent::__construct( array(
			'singular' => 'Signed Document',     // Singular name of the listed records.
			'plural'   => 'Signed Documents',     // Plural name of the listed records.
			'ajax'     => false,                 // Does this table support ajax?
		) );
    }

    /**
     * Get a list of columns. The format is:
     * 'internal-name' => 'Title'
     */
    public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', // Render a checkbox instead of text.
			'uuid'     => _x( 'UUID', 'Column label', 'wp-pgpsign-list-table' ),
			'created'  => _x( 'Created', 'Column label', 'wp-pgpsign-list-table' ),
			'referral' => _x( 'Referral', 'Column label', 'wp-pgpsign-list-table' ),
            'keyid'    => _x( 'KeyID', 'Column label', 'wp-pgpsign-list-table' ),
            'pgpsign'  => _x( 'Signature', 'Column label', 'wp-pgpsign-list-table' ),
            'remark'   => _x( 'Remark', 'Column label', 'wp-pgpsign-list-table' ),
		);

		return $columns;
	}

    /**
     * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
     * The second format will make the initial sorting order be descending
     */
    protected function get_sortable_columns() {
		$sortable_columns = array(
			'created'  => array( 'created', false ),
			'referral' => array( 'referral', false ),
			'keyid'    => array( 'keyid', false ),
		);

		return $sortable_columns;
	}

    /**
     * Get default column value.
	 *
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
    protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			// case 'uuid': 
			// return $item->uuid; // See function column_uuid($item)
			// case 'created':
			// 	return $item->created; //  See function column_created( $item )
			// case 'referral':
			// 	return $item->referral; // See function column_referral( $item )
			case 'keyid':
				return $item->keyid;
			// case 'pgpsign':
			// 	return $item->pgpsign); // See function column_pgpsign( $item )
			case 'remark':
				return $item->remark;
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}

    /**
     * Get value for checkbox column.
     */
    protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item->uuid                // The value of the checkbox should be the record's ID.
		);
	}

    /**
     * Get UUID column value.
     */
    protected function column_uuid( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		/*

		// Build edit row action.
		$edit_query_args = array(
			'page'             => $page,
			'action'           => 'edit',
			'Signed Document'  => $item->uuid,
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editpgpsign_' . $item->uuid ) ),
			_x( 'Edit', 'List table row action', 'wp-pgpsign-list-table' )
		);
		
		*/

		// Build delete row action.
		$delete_query_args = array(
			'page'             => $page,
			'action'           => 'delete',
			'Signed Document'  => $tiem->uuid,
		);

		$actions['delete'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletepgpsign_' . $tiem->uuid ) ),
			_x( 'Delete', 'List table row action', 'wp-pgpsign-list-table' )
		);

		// Return the UUID contents.
		$config = get_option('pgpsign_options');
		return sprintf( '<a href="%1$s" target="_blank">%2$s</a>%3$s',
			get_site_url() . '/' . $config['permalink'] . '/' . $item->uuid,
			$item->uuid,
			$this->row_actions( $actions )
		);
	}

	/**
	 * Get Created date value.
	 */
	protected function column_created( $item ) {
		// return $item->created;
		return date_i18n( get_option( 'date_format' ) . ' \a\t ' . get_option( 'time_format' ), strtotime( $item->created ) );
	}

	/**
	 * 
	 */
	protected function column_referral( $item ) {
		return '<a href="' . $item->referral . '" target="_blank">' . $item->referral . '</a>';
	}

	/**
	 * Get PGPSign value.
	 */
	protected function column_pgpsign( $item ) {

		// Truncate the signature @ 32 caracteres 
		return substr($item->pgpsign, 0, 32);
	}

    /**
     * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
     */
    protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Delete', 'List table bulk action', 'wp-pgpsign-list-table' ),
		);

		return $actions;
	}

    /**
     * Handle bulk actions.
     * 
     * @see $this->prepare_items()
     */
    protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}
	}

    /**
     * Prepares the list of items for displaying.
     * 
     * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
     */

    function prepare_items() {
		global $wpdb;

        $per_page = 10;

		// check if a search was performed.
		// Source: https://wpmudev.com/blog/wordpress-admin-tables/
		$user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	

        /*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

        /*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/*
		 * GET THE DATA!
		 * 
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our dummy data.
		 * 
		 * In a real-world situation, this is probably where you would want to 
		 * make your actual database query. Likewise, you will probably want to
		 * use any posted sort or pagination data to build a custom query instead, 
		 * as you'll then be able to use the returned query data immediately.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */
		$table_name = $wpdb->prefix . 'pgpsign';
		$orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'created';
		$order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'DESC';
		$records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $orderby $order");

		/**
		 * Filter the data in case of a search
		 */
		if( $user_search_key ) {
			$records = $this->filter_records( $records, $user_search_key );
		}

        /*
		 * REQUIRED for pagination.
		 */
		$current_page = $this->get_pagenum();

        /*
		 * REQUIRED for pagination.
		 */
		$total_items = count( $records );

        /*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$records = array_slice( $records, ( ( $current_page - 1 ) * $per_page ), $per_page );		

        /*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $records;

        /**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
    }

	function no_items() {
		_e( 'Nothing found!' );
	}
	
	/**
	 * Function to filter the records from MySQL if the user has used the search box
	 * Source: https://wpmudev.com/blog/wordpress-admin-tables/
	 */
	public function filter_records( $records, $search_key ) {
		$filtered_records = array_values( array_filter( $records, function( $row ) use( $search_key ) {
			foreach( $row as $row_val ) {
				if( stripos( $row_val, $search_key ) !== false ) {
					return true;
				}				
			}			
		} ) );
	
		return $filtered_records;
	}
}