<?php

/**
 * Global Variables
 */
global $wpdb;
global $mail_table;

$mail_table = $wpdb->base_prefix . 'wa_mail';


/**
 * Create wa_mail Table
 */
if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $mail_table ) ) ) ) {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();

	dbDelta("CREATE TABLE {$mail_table} (
		`id`        BIGINT(20) unsigned NOT NULL auto_increment,
		`to`        longtext NOT NULL DEFAULT '',
		`from`      longtext NOT NULL DEFAULT '',
		`pending`   longtext NOT NULL DEFAULT '',
		`subject`   longtext NOT NULL DEFAULT '',
		`message`   longtext NOT NULL DEFAULT '',
		`timestamp` int NOT NULL default 0,
		`status`    VARCHAR(255) NOT NULL DEFAULT '',
		PRIMARY KEY (id)
	) $charset_collate");
}


/**
 * Create a new List Table
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/screen.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Temp_Mails_List_Table extends WP_List_Table {

	/**
	 * Set up a constructor
	 */
	function __construct() {
		parent::__construct( array(
			'singular' => 'temp-mail',
			'plural'   => 'temp-mails',
			'ajax'     => false
		) );
	}


	/**
	 * This method is called when the parent class can't find a method specifically build for a given column.
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'subject':
				return sprintf('%1$s<br><p class="description">%2$s</p>%3$s',
					$item['subject'],
					sprintf( '%s: %s', __( 'From', 'weadapt' ), esc_attr( $item['from'] ) ),
					$this->row_actions( array(
						'delete' => sprintf( '<a href="?page=%s&action=delete&%s=%s">%s</a>',
							$_REQUEST['page'],
							$this->_args['singular'],
							$item['id'],
							__( 'Delete', 'weadapt' )
						),
					) )
				);
				break;

			case 'to':
			case 'pending':
				$user_IDs  = maybe_unserialize( $item[$column_name] );
				$user_data = [];

				if ( ! empty( $user_IDs ) ) {
					foreach ( $user_IDs as $user_ID ) {
						$user = new WP_User( $user_ID );

						$user_data[] = sprintf( '%s (%s)', $user->user_email, $user_ID );
					}
				}
				ob_start();
					if ( ! empty( $user_data ) ) {
						$visible_ids = array_slice($user_data, 0, 6);
						$hidden_ids  = array_slice($user_data, 6);

						echo implode( '<br>', $visible_ids );

						if ( ! empty( $hidden_ids ) ) {
							$unique_id = get_unique_ID( 'input' );

							?>
								<div class="wrapper">
									<input id="input-<?php echo esc_attr( $unique_id ); ?>" type="checkbox">
									<label for="input-<?php echo esc_attr( $unique_id ); ?>">
										<span class="show"><?php _e( 'Show more...', 'weadapt' ); ?></span>
										<span class="hide"><?php _e( 'Show less...', 'weadapt' ); ?></span>
									</label>
									<div><?php echo implode( '<br>', $hidden_ids ); ?></div>
								</div>
							<?php

						}
					}
					else {
						echo '—';
					}

				return ob_get_clean();
				break;

			case 'timestamp':
				return date_i18n( 'Y/n/j H:i:s', $item[$column_name] );
				break;

			default:
			return $item[$column_name];
				break;
		}
	}

	/**
	 * Column Cb Output
	 */
	function column_cb($item){
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item['id']
		);
	}


	/**
	 * The table's columns and titles.
	 */
	function get_columns(){
		return array(
			'cb'        => '<input type="checkbox" />',
			'subject'   => __( 'Subject', 'weadapt' ),
			'message'   => __( 'Message', 'weadapt' ),
			'to'        => __( 'To', 'weadapt' ),
			'pending'   => __( 'Pending', 'weadapt' ),
			'timestamp' => __( 'Date', 'weadapt' ),
			'status'    => __( 'Status', 'weadapt' )
		);
	}


	/**
	 * Sortable Columns
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'subject'   => array( 'subject' ),
			'message'   => array( 'message' ),
			'timestamp' => array( 'timestamp' ),
			'status'    => array( 'status' )
		);
		return $sortable_columns;
	}


	/**
	 * Include bulk actions
	 */
	function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'weadapt' )
		);
	}


	/**
	 * Process Bulk Action
	 *
	 * @see $this->prepare_items()
	 */
	function process_bulk_action() {
		global $wpdb;
		global $mail_table;

		// Multiple
		if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'bulk-' . $this->_args['plural'] ) ) {
				wp_die( __( 'Nope! Security check failed!', 'weadapt' ) );
			}
			else {
				$db_IDs = isset( $_GET[$this->_args['singular']] ) ? wp_parse_id_list( $_GET[$this->_args['singular']] ) : [];

				if ( ! empty( $db_IDs ) && is_array( $db_IDs ) ) {
					switch ( $this->current_action() ) {
						case 'delete':
							foreach ( $db_IDs as $db_ID ) {
								if ( '0' !== $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $mail_table WHERE id = %d", $db_ID ) ) ) {
									$wpdb->delete( $mail_table, [
										'id' => $db_ID
									] );
								}
							}
							break;
					}
				}
			}
		}

		switch ( $this->current_action() ) {
			case 'delete':
				$db_ID = isset( $_GET[$this->_args['singular']] ) ? (int) $_GET[$this->_args['singular']] : 0;

				if ( ! empty( $db_ID ) && ( '0' !== $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $mail_table WHERE id = %d", $db_ID ) ) ) ) {
					$wpdb->delete( $mail_table, [
						'id' => $db_ID
					] );
				}

				break;
		}
	}


	/**
	 * Filters
	 *
	 * @see $this->extra_tablenav()
	 */
	function extra_tablenav( $which ) {
		if ( 'top' === $which ) :
			$mail_from = ! empty( $_GET['mail_from'] ) ? esc_attr( $_GET['mail_from'] ) : '';
		?>
			<div class="alignleft actions">
				<select name="mail_from">
					<option value=""><?php _e( 'All sites', 'weadapt' ); ?></option>

					<?php foreach ( get_sites() as $key => $site ) : ?>
						<option value="<?php echo $site->domain; ?>"<?php selected( $mail_from, $site->domain ); ?>><?php echo get_blog_details( $site->blog_id )->blogname; ?></option>
					<?php endforeach; ?>
				</select>
				<input type="submit" class="button" value="<?php _e( 'Filter', 'weadapt' ); ?>" name="filter_action">
			</div>
		<?php
		endif;
	}


	/**
	 * Prepare Data for Display
	 */
	function prepare_items() {
		global $wpdb;
		global $mail_table;

		// Define Per Page
		$per_page = 20;

		// Define Column Headers
		$hidden   = array();
		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );


		// Process bulk actions
		$this->process_bulk_action();


		// Get DB Data
		if ( ! empty( $_GET['mail_from'] ) ) {
			$data = $wpdb->get_results( "SELECT * FROM $mail_table WHERE `from` LIKE '%" . $_GET['mail_from'] . "%'", ARRAY_A );
		}
		else {
			$data = $wpdb->get_results( "SELECT * FROM $mail_table", ARRAY_A );
		}

		// Checks for sorting input
		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'timestamp';
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';
			$result  = strcmp( $a[$orderby], $b[$orderby] );

			return ( 'asc' === $order ) ? $result : -$result;
		}
		usort( $data, 'usort_reorder' );


		// Pagination
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items  = $data;

		// Register pagination options
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}
}


/**
 * Define Admin Page
 */
add_action('admin_menu', function() {
	add_menu_page(
		__( 'Temp Mails', 'weadapt' ),
		__( 'Temp Mails', 'weadapt' ),
		'activate_plugins',
		'temp-mails',
		'temp_mail_render_list_page',
		'dashicons-email-alt',
		100
	);
});


/**
 * Render Admin Page
 */
function temp_mail_render_list_page(){
	$list_table = new Temp_Mails_List_Table();

	$list_table->prepare_items();
	?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<form action="<?php echo get_admin_url( null, '/admin.php?page=temp-mails' ); ?>" id="posts-filter" method="GET">
			<input type="hidden" name="page" value="temp-mails" />
			<?php $list_table->display(); ?>
		</form>
	</div>
	<?php
}