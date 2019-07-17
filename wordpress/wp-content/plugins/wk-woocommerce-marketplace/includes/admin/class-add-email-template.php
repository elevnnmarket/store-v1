<?php

if ( ! defined( 'ABSPATH' ) )
		exit;

global $wpdb;
$table_name = $wpdb->prefix.'emailTemplate';
$results = $sql = '';
$error = array();

if ( isset( $_POST[ 'submit_template' ] ) ) {

		$title 			= strip_tags( $_POST['txtbx'] );
		$base 			= $_POST['frstclr'];
		$body 			= $_POST['scndclr'];
		$background = $_POST['thrdclr'];
		$txt 				= $_POST['frthclr'];

		if( empty($title) || empty($base) || empty($body) || empty($background) || empty($txt) ) {
				$error['empty'] = __( 'Please fill all details.', 'marketplace' );
		}
		else {
				if ( isset( $_GET['user'] ) ) {
						$id = $_GET['user'];
						$sql = $wpdb->update(
								$table_name,
								array(
										'basecolor'	=> $base,
										'bodycolor'	=> $body,
										'backgroundcolor'	=> $background,
										'textcolor'	=> $txt
								),
								array(
										'id'	=> $id
								),
								array(
										'%s',
										'%s',
										'%s',
										'%s'
								),
								array( '%d' )
						);
				}
				else {
					if ( preg_match( "/^[a-zA-Z0-9\s-, ]+$/", $title ) ) {
						$sql = $wpdb->insert(
								$table_name,
								array(
										'title'			=> $title,
										'basecolor'	=> $base,
										'bodycolor'	=> $body,
										'backgroundcolor'	=> $background,
										'textcolor'	=> $txt,
										'status'		=> 'publish'
								),
								array(
										'%s',
										'%s',
										'%s',
										'%s',
										'%s',
										'%s'
								)
						);
					} else {
						$error['invalid'] = __( 'Please enter the valid template name.', 'marketplace' );
					}
				}
				if ( $sql ) {
						if ( isset( $_GET['user'] ) ) {
								echo "<div class='notice notice-success'><h4>Template updated successfully.</h4></div>";
						}
						else {
								echo "<div class='notice notice-success'><h4>Template added successfully.</h4></div>";
						}
				}
  	}
}


if( isset( $_GET[ 'user' ] ) ) {
	if (  ! empty( $_GET[ 'user' ] ) ) {
		global $wpdb;
		$id = filter_input( INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT );
		$sql = "SELECT * FROM $table_name WHERE id = $id";
		$results = $wpdb->get_results($sql);
		if ( $results) {
			$results = $results[0];
		} else {
			?>
			<div class="notice notice-error is-dismissible">
        <p><?php _e( 'Invalid Id.', 'marketplace' ); ?></p>
    	</div>
			<?php
		}
	} else {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'Id cannot be empty.', 'marketplace' ); ?></p>
		</div>
		<?php
	}
}

?>

<div class="wrap">

		<?php
		if( isset( $_GET[ 'user' ] ) ) :
				echo '<h1 class="wp-heading-inline">Edit Template</h1>';
		else :
				echo '<h1 class="wp-heading-inline">Add New Template</h1>';
		endif;
		echo '<a href="?page=class-email-templates" class="page-title-action">Back</a>';

		if ( $error ) {
			foreach ($error as $key => $value) {
				echo '<div class="notice notice-error is-dismissible">
								<p>' . $value . '</p>
				</div>';
			}
		}
		?>

		<form method="POST" id="emailtemplate">

				<div id="titlediv" style="margin-bottom:30px;">
						<div id="titlewrap">
								<input type="text" name="txtbx" value="<?php if( $results ) echo esc_attr( $results->title ); ?>" id="title" class="tmplt_name" spellcheck="true" autocomplete="off" placeholder="Enter title here" <?php if( $results ) echo 'readonly'; ?> />
						</div>
						<p class="description"><?php if( $results && $results->title ) esc_html_e( 'Title cannot be changed.', 'marketplace' ); ?></p>
				</div>

				<table class="form-table">
						<tbody>

  							<tr valign="top"><th><?php esc_html_e( 'Style Options', 'marketplace' ); ?> </th><td><hr></td></tr>

								<tr valign="top">
			  						<th scope="row" class="titledesc"><?php esc_html_e( 'Base color', 'marketplace' ); ?></th>
		        				<td class="forminp">
											<input type="text" name="frstclr" id="clr1" class="frstclrchooser" value="<?php if( $results ) echo $results->basecolor; else echo '#8a8a8a'; ?>">
											<p class="description"><?php esc_html_e( 'The base color for this Marketplace email template. Default #8a8a8a.', 'marketplace' ); ?></p>
										</td>
    						</tr>

								<tr valign="top">
					          <th scope="row" class="titledesc"><?php esc_html_e( 'Body background color', 'marketplace' ); ?></th>
						      	<td class="forminp">
											<input type="text" name="scndclr" id="clr2" class="frstclrchooser" value="<?php if( $results ) echo $results->bodycolor; else echo '#ffffff'; ?>" >
											<p class="description"><?php esc_html_e( 'The main body background color. Default #ffffff.', 'marketplace' ); ?></p>
										</td>
						    <tr>

								<tr valign="top">
		  							<th scope="row" class="titledesc"><?php esc_html_e( 'Background color', 'marketplace' ); ?></th>
		    						<td class="forminp">
											<input type="text" name="thrdclr" id="clr3"  class="thirdclrchooser" value="<?php if( $results ) echo $results->backgroundcolor; else echo '#f7f7f7'; ?>" >
											<p class="description"><?php esc_html_e( 'The background color for this Marketplace email template. Default #f7f7f7.', 'marketplace' ); ?></p>
										</td>
								</tr>

							  <tr valign="top">
							   		<th scope="row" class="titledesc"><?php esc_html_e( 'Body text color', 'marketplace' ); ?></th>
						       	<td class="forminp">
											<input type="text" name="frthclr" id="clr4" class="frstclrchooser" value="<?php if( $results ) echo $results->textcolor; else echo '#3c3c3c'; ?>" >
											<p class="description"><?php esc_html_e( 'The main body text color. Default #3c3c3c.', 'marketplace' ); ?></p>
										</td>
							   </tr>

						</tbody>

				</table>

 				<p class="submit"><input type="submit" name="submit_template" value="Save" class="button button-primary saveBtn"></p>

		</form>

</div>
