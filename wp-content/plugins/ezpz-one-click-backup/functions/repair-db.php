<?php
function ezpz_ocb_db_repair(){
	$optimize = true;
	$okay = true;
	$problems = array();
	$tables = $wpdb->tables();

	// Sitecategories may not exist if global terms are disabled.
	if ( is_multisite() && ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->sitecategories'" ) )
		unset( $tables['sitecategories'] );

	$tables = array_merge( $tables, (array) apply_filters( 'tables_to_repair', array() ) ); // Return tables with table prefixes.

	// Loop over the tables, checking and repairing as needed.
	foreach ( $tables as $table ) {
		$check = $wpdb->get_row("CHECK TABLE $table");

		tmp_write('<p>');
		if ( 'OK' == $check->Msg_text ) {
			/* translators: %s: table name */
			tmp_write(sprintf( __( 'The %s table is okay.' ), $table ));
		} else {
			/* translators: 1: table name, 2: error message, */
			tmp_write(sprintf( __( 'The %1$s table is not okay. It is reporting the following error: %2$s.  WordPress will attempt to repair this table&hellip;' ) , $table, "<code>$check->Msg_text</code>" ));

			$repair = $wpdb->get_row("REPAIR TABLE $table");

			tmp_write('<br />&nbsp;&nbsp;&nbsp;&nbsp;');
			if ( 'OK' == $check->Msg_text ) {
				/* translators: %s: table name */
				tmp_write(sprintf( __( 'Successfully repaired the %s table.' ), $table ));
			} else {
				/* translators: 1: table name, 2: error message, */
				tmp_write(sprintf( __( 'Failed to repair the  %1$s table. Error: %2$s' ), $table, "<code>$check->Msg_text</code>" ) . '<br />');
				$problems[$table] = $check->Msg_text;
				$okay = false;
			}
		}

		if ( $okay && $optimize ) {
			$check = $wpdb->get_row("ANALYZE TABLE $table");

			tmp_write('<br />&nbsp;&nbsp;&nbsp;&nbsp');
			if ( 'Table is already up to date' == $check->Msg_text )  {
				/* translators: %s: table name */
				tmp_write(sprintf( __( 'The %s table is already optimized.' ), $table ));
			} else {
				$check = $wpdb->get_row("OPTIMIZE TABLE $table");

				tmp_write('<br />&nbsp;&nbsp;&nbsp;&nbsp');
				if ( 'OK' == $check->Msg_text || 'Table is already up to date' == $check->Msg_text ) {
					/* translators: %s: table name */
					tmp_write(sprintf( __( 'Successfully optimized the %s table.' ), $table ));
				} else {
					/* translators: 1: table name, 2: error message, */
					tmp_write(sprintf( __( 'Failed to optimize the %1$s table. Error: %2$s' ), $table, "<code>$check->Msg_text</code>" ));
				}
			}
		}
		tmp_write('</p>');
	}

	if ( !empty($problems) ) {
		tmp_write(sprintf('<p>'.__('Some database problems could not be repaired. Please copy-and-paste the following list of errors to the <a href="%s">WordPress support forums</a> to get additional assistance.').'</p>', 'http://wordpress.org/support/forum/3'));
		$problem_output = array();
		foreach ( $problems as $table => $problem )
			$problem_output[] = "$table: $problem";
		tmp_write('<textarea name="errors" id="errors" rows="20" cols="60">' . esc_textarea( implode("\n", $problem_output) ) . '</textarea>');
	} else {
		tmp_write('<p>'.__('Repairs complete.  Please remove the following line from wp-config.php to prevent this page from being used by unauthorized users.')."</p><code>define('WP_ALLOW_REPAIR', true);</code>");
	}
}
?>
