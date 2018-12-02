<?php
if (!defined('ABSPATH')) die('No direct access.');
class SCE_Output {

	/**
	 * Holds options for SCE Options
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $options
	 */
	private $options = array();

	public function __construct() {

		// Get SCE options
		$options = get_site_option( 'sce', false );
		if( false === $options ) return;
		if( is_array( $options ) ) {
			$this->options = $options;
		}

		$this->init_filters();
		$this->init_actions();
	}

	/**
	 * Initializes SCE's various filters.
	 *
	 * Initializes SCE's various filters.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function init_filters() {
		add_filter( 'sce_wrapper_class', array( $this, 'output_theme_class' ) );
		add_filter( 'sce_comment_time', array( $this, 'modify_timer' ) );
		add_filter( 'sce_show_timer', array( $this, 'show_timer' ) );
		add_filter( 'sce_text_save', array( $this, 'save_button_text' ) );
		add_filter( 'sce_text_cancel', array( $this, 'save_cancel_text' ) );
		add_filter( 'sce_text_delete', array( $this, 'save_delete_text' ) );
		add_filter( 'sce_text_edit', array( $this, 'edit_text' ) );
		add_filter( 'sce_allow_delete_confirmation', array( $this, 'allow_delete_confirmation' ) );
		add_filter( 'sce_allow_delete', array( $this, 'allow_deletion' ) );
		add_filter( 'sce_loading_img', array( $this, 'loading_img' ) );
		add_filter( 'sce_confirm_delete', array( $this, 'message_confirm_delete' ) );
		add_filter( 'sce_comment_deleted', array( $this, 'message_comment_deleted' ) );
		add_filter( 'sce_comment_deleted_error', array( $this, 'message_comment_deleted_error' ) );
		add_filter( 'sce_empty_comment', array( $this, 'message_empty_comment' ) );
	}

	/**
	 * Initializes SCE's various actions.
	 *
	 * Initializes SCE's various actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function init_actions() {

	}

	/**
	 * Returns an error when a comment is empty.
	 *
	 * Returns an error when a comment is empty.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $message Empty comment error
	 * @return string New empty comment error
	 */
	public function message_empty_comment( $message ) {
		return isset( $this->options['comment_empty_error'] ) ? $this->options['comment_empty_error'] : $message;
	}

	/**
	 * Returns a delete error.
	 *
	 * Returns a delete error.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $message Delete error message
	 * @return string New delete error message
	 */
	public function message_comment_deleted_error( $message ) {
		return isset( $this->options['comment_deleted_error'] ) ? $this->options['comment_deleted_error'] : $message;
	}

	/**
	 * Returns a delete confirmation when a comment is removed.
	 *
	 * Returns a delete confirmation when a comment is removed.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $message Delete removal message
	 * @return string New delete removal message
	 */
	public function message_comment_deleted( $message ) {
		return isset( $this->options['comment_deleted'] ) ? $this->options['comment_deleted'] : $message;
	}

	/**
	 * Returns a delete confirmation modal text.
	 *
	 * Returns a delete confirmation modal text.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $message Delete confirmation message
	 * @return string New delete confirmation message
	 */
	public function message_confirm_delete( $message ) {
		return isset( $this->options['confirm_delete'] ) ? $this->options['confirm_delete'] : $message;
	}

	/**
	 * Returns a new loading image when saving a comment.
	 *
	 * Returns a new loading image when saving a comment.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $loading_image_url Loading image url
	 * @return string New loading image url
	 */
	public function loading_img( $loading_image_url ) {
		$loading_image = isset( $this->options['loading_image'] ) ? $this->options['loading_image'] : $loading_image_url;
		return $loading_image;
	}

	/**
	 * Returns whether a delete option is available.
	 *
	 * Returns whether a delete option is available.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param bool $allow_deletion Whether to allow comment deletion
	 * @return bool Whether to allow comment deletion
	 */
	public function allow_deletion( $allow_deletion ) {
		$allow_deletion = isset( $this->options['allow_delete'] ) ? $this->options['allow_delete'] : $allow_deletion;
		return $allow_deletion;
	}

	/**
	 * Returns whether a delete confirmation modal appears when deleting a comment.
	 *
	 * Returns whether a delete confirmation modal appears when deleting a comment.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param bool $allow_deletion_confirmation Whether to allow confirmation modal
	 * @return bool Whether to allow confirmation modal
	 */
	public function allow_delete_confirmation( $allow_delete_confirmation ) {
		$allow_confirmation = isset( $this->options['allow_delete_confirmation'] ) ? $this->options['allow_delete_confirmation'] : $allow_delete_confirmation;
		return $allow_confirmation;
	}

	/**
	 * Returns the edit text.
	 *
	 * Returns the edit text.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $edit_text The main edit text for SCE
	 * @return string New edit text
	 */
	public function edit_text( $edit_text ) {
		$new_edit_text = isset( $this->options['click_to_edit_text'] ) ? $this->options['click_to_edit_text'] : '';
		if ( '' === $new_edit_text ) return $edit_text;
		return $new_edit_text;
	}

	/**
	 * Returns button text for delete button.
	 *
	 * Returns button text for delete button.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $button_text Button text
	 * @return string New button text
	 */
	public function save_delete_text( $button_text ) {
		$new_button_text = isset( $this->options['delete_text'] ) ? $this->options['delete_text'] : '';
		if ( '' === $new_button_text ) return $button_text;
		return $new_button_text;
	}

	/**
	 * Returns button text for cancel button.
	 *
	 * Returns button text for cancel button.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $button_text Button text
	 * @return string New button text
	 */
	public function save_cancel_text( $button_text ) {
		$new_button_text = isset( $this->options['cancel_text'] ) ? $this->options['cancel_text'] : '';
		if ( '' === $new_button_text ) return $button_text;
		return $new_button_text;
	}

	/**
	 * Returns button text for save button.
	 *
	 * Returns button text for save button.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $button_text Button text
	 * @return string New button text
	 */
	public function save_button_text( $button_text ) {
		$new_button_text = isset( $this->options['save_text'] ) ? $this->options['save_text'] : '';
		if ( '' === $new_button_text ) return $button_text;
		return $new_button_text;
	}

	/**
	 * Returns whether to show a timer.
	 *
	 * Returns whether to show a timer.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param bool $show_timer Whether to show the timer or not
	 * @return bool Whether to show the timer or not
	 */
	public function show_timer( $show_timer ) {
		$new_show_timer = isset( $this->options['show_timer'] ) ? $this->options['show_timer'] : '';
		if ( '' === $new_show_timer ) return $show_timer;
		return $new_show_timer;
	}

	/**
	 * Returns a new timer.
	 *
	 * Returns a new timer.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param int $timer Time in minutes to edit the comment
	 * @return int New time in minutes
	 */
	public function modify_timer( $timer ) {
		$new_timer = isset( $this->options['timer'] ) ? $this->options['timer'] : false;
		if ( false === $new_timer ) return $timer;
		return $new_timer;
	}

	/**
	 * Returns a theme class.
	 *
	 * Returns a theme class.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param array $classes SCE Wrapper class
	 * @return array $classes New SCE theme classes
	 */
	public function output_theme_class( $classes = array() ) {
		$theme = isset( $this->options['button_theme'] ) ? $this->options['button_theme'] : false;
		if ( false === $theme ) return $classes;
		$classes[] = $theme;
		return $classes;
	}

}