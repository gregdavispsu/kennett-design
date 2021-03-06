<?php

class WpSmproErrorLog {
	var $_limit = 100;

	function get_all_errors() {
		$errors = get_option( 'sp_error_log' );

		return $errors ? $errors : array();
	}

	function get_all_notices() {
		$notices = get_option( 'sp_notice_log' );

		return $notices ? $notices : array();
	}

	function purge_errors() {
		update_option( 'sp_error_log', array() );
	}

	function purge_notices() {
		update_option( 'sp_notice_log', array() );
	}

	function error( $function, $exception ) {
		WpSmproErrorRegistry::store( $exception );
		$info = is_object( $exception ) && method_exists( $exception, 'getMessage' )
			? $exception->getMessage()
			: $exception;

		$this->_update_error_queue( array(
			'date'    => current_time( 'timestamp' ),
			'area'    => $function,
			'user_id' => get_current_user_id(),
			'type'    => 'exception',
			'info'    => $info
		) );
	}

	function notice( $msg ) {
		$this->_update_notice_queue( array(
			'date'    => current_time( 'timestamp' ),
			'user_id' => get_current_user_id(),
			'message' => $msg
		) );
	}

	function _update_error_queue( $error ) {
		$errors = $this->get_all_errors();
		if ( count( $errors ) >= $this->_limit ) {
			$errors = array_slice( $errors, ( ( $this->_limit * - 1 ) - 1 ), $this->_limit - 1 );					 	     	 	  			
		}
		$errors[] = $error;
		update_option( 'sp_error_log', $errors );
	}

	function _update_notice_queue( $notice ) {
		$notices = $this->get_all_notices();
		if ( count( $notices ) >= $this->_limit ) {
			$notices = array_slice( $notices, - $this->_limit );
		} // * -1)), $this->_limit-1);
		$notices[] = $notice;
		update_option( 'sp_notice_log', $notices );
	}
}
global $log;
$log = new WpSmproErrorLog();