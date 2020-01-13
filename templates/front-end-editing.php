<?php
/**
 * Template for front-end editing.
 *
 * @package SCEOptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
}
if ( ! current_user_can( 'moderate_comments' ) ) {
	die( 'User must be able to moderate comments' );
}
wp_register_script(
	'sce-frontend-editing',
	SCE_Options::get_instance()->get_plugin_url( '/js/front-end-editing.js' ),
	array( 'jquery' ),
	SCE_OPTIONS_VERSION,
	false
);
wp_localize_script(
	'sce-frontend-editing',
	'sce_frontend',
	array(
		'saving'   => __( 'Saving...', 'simple-comment-editing-options' ),
		'save'     => __( 'Save', 'simple-comment-editing-options' ),
		'saved'    => __( 'Comment Has Been Saved', 'simple-comment-editing-options' ),
		'deleting' => __( 'Deleting...', 'simple-comment-editing-options' ),
		'deleted'  => __( 'Comment Has Been Deleted', 'simple-comment-editing-options' ),
		'ajaxurl'  => admin_url( 'admin-ajax.php' ),
	)
);
wp_register_style(
	'bootstrap-reboot',
	SCE_Options::get_instance()->get_plugin_url( '/bootstrap/css/bootstrap-reboot.min.css' ),
	array(),
	SCE_OPTIONS_VERSION,
	'all'
);
wp_register_style(
	'bootstrap-grid',
	SCE_Options::get_instance()->get_plugin_url( '/bootstrap/css/bootstrap-grid.min.css' ),
	array( 'bootstrap-reboot' ),
	SCE_OPTIONS_VERSION,
	'all'
);
wp_register_style(
	'bootstrap',
	SCE_Options::get_instance()->get_plugin_url( '/bootstrap/css/bootstrap.min.css' ),
	array( 'bootstrap-reboot' ),
	SCE_OPTIONS_VERSION,
	'all'
);
$sce_comment        = get_comment( $comment_id );
$comment_author     = $this->format_comment_text( $sce_comment->comment_author );
$comment_email      = $sce_comment->comment_author_email;
$comment_content    = $this->format_comment_text( $sce_comment->comment_content );
$comment_url        = $sce_comment->comment_author_url;
$sce_comment_status = $sce_comment->comment_approved;
$sce_admin_edit_url = add_query_arg(
	array(
		'action' => 'editcomment',
		'c'      => $comment_id,
	),
	admin_url( 'comment.php' )
);
?>
<html>
	<head>
		<?php
		wp_print_styles(
			array(
				'bootstrap-reboot',
				'bootstrap',
				'bootstrap-grid',
			)
		);
		wp_print_scripts(
			array(
				'sce-frontend-editing',
			)
		);
		?>
		<style>
			body {
				padding: 50px;
			}
		</style>
	</head>
	<body>
		<div class="sce-wrapper" style="height; auto; overflow: hidden">
			<h1><?php esc_html_e( 'Comment Editing', 'simple-comment-editing-options' ); ?></h1>
			<hr />
			<form>
				<div class="form-group row">
					<label for="sce-name" class="col-sm-2 col-form-label"><?php esc_html_e( 'Name', 'simple-comment-editing-options' ); ?></label>
					<div class="col-sm-10">
					<input type="text" value="<?php echo esc_attr( $comment_author ); ?>" class="form-control" id="sce-name" placeholder="<?php esc_html_e( 'Enter a name', 'simple-comment-editing-options' ); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label for="sce-email" class="col-sm-2 col-form-label"><?php esc_html_e( 'Email', 'simple-comment-editing-options' ); ?></label>
					<div class="col-sm-10">
					<input type="text" value="<?php echo esc_attr( $comment_email ); ?>" class="form-control" id="sce-email" placeholder="<?php esc_html_e( 'Enter an email address', 'simple-comment-editing-options' ); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label for="sce-url" class="col-sm-2 col-form-label"><?php esc_html_e( 'URL', 'simple-comment-editing-options' ); ?></label>
					<div class="col-sm-10">
					<input type="text" value="<?php echo esc_attr( esc_url_raw( $comment_url ) ); ?>" class="form-control" id="sce-url" placeholder="<?php esc_html_e( 'Enter a URL', 'simple-comment-editing-options' ); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label for="sce-comment" class="col-sm-2 col-form-label"><?php esc_html_e( 'Comment', 'simple-comment-editing-options' ); ?></label>
					<div class="col-sm-10">
						<textarea class="form-control" rows="5" id="sce-content"><?php echo wp_kses_post( $comment_content ); ?></textarea>
					</div>
				</div>
				<fieldset class="form-group">
					<div class="row">
					<legend class="col-form-label col-sm-2 pt-0"><?php esc_html_e( 'Comment Status', 'simple-comment-editing-options' ); ?></legend>
					<div class="col-sm-10">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="status" id="status-approved" value="1" <?php checked( 1, $sce_comment_status ); ?>>
							<label class="form-check-label" for="status-approved">
								<?php esc_html_e( 'Approved', 'simple-comment-editing-options' ); ?>
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="status" id="status-pending" value="0" <?php checked( 0, $sce_comment_status ); ?>>
							<label class="form-check-label" for="status-pending">
								<?php esc_html_e( 'Pending', 'simple-comment-editing-options' ); ?>
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="status" id="status-spam" value="spam" <?php checked( 'spam', $sce_comment_status ); ?>>
							<label class="form-check-label" for="status-spam">
								<?php esc_html_e( 'Spam', 'simple-comment-editing-options' ); ?>
							</label>
						</div>
					</div>
					</div>
				</fieldset>
				<hr />
				<div class="form-group row">
					<div class="col-sm-12">
						<input type="hidden" id="sce-comment-id" value="<?php echo esc_attr( $comment_id ); ?>" />
						<?php
						wp_nonce_field( 'sce-modify-comment-' . $comment_id, 'sce_nonce' );
						?>
						<button type="submit" class="btn btn-success sce-save-comment"><?php esc_html_e( 'Save Comment', 'simple-comment-editing-options' ); ?></button>&nbsp;&nbsp;<a class="btn btn-secondary sce-view-admin" href="<?php echo esc_url( $sce_admin_edit_url ); ?>" style="color: #FFF;" target="_blank"><?php esc_html_e( 'View in Admin', 'simple-comment-editing-options' ); ?></a>&nbsp;&nbsp;<button class="btn btn-secondary sce-close"><?php esc_html_e( 'Close', 'simple-comment-editing-options' ); ?></button>
					</div>
					<div class="col-sm-12" style="text-align: right;">
						<button type="submit" class="btn btn-danger sce-delete-comment"><?php esc_html_e( 'Delete Comment', 'simple-comment-editing-options' ); ?></button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
