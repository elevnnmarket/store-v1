<?php
/**
 * Front function handler.
 */
if (!defined('ABSPATH')) {
    exit;
}

?> <div class="woocommerce-account">

	<?php

    apply_filters('mp_get_wc_account_menu', 'marketplace');
    $current_user = wp_get_current_user();
    global $wpdb;

    $buser = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge_assign where u_id='$current_user->ID'");
    if (!$buser) {
        echo '<b>'.__('No badge Assigned', 'wk-seller-badge').'</b>';
    } else {
        $i = 0;
        foreach ($buser as $key) {
            $badge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where id='$key->b_id' and status ='1'");

            foreach ($badge as $key3) {
                ++$i;
            }
        }

        if ($i == 0) {
            echo '<b>'.__('No badge Assigned', 'wk-seller-badge').'</b>';
        } else {
            ?>
			<div class="favourite-seller woocommerce-MyAccount-content">

				<table class="shop-fol" >
					<tr role="row">
						<th><?php esc_html_e('Badge(s) Assigned', 'wk-seller-badge'); ?></th>
						<th><?php esc_html_e('Badge(s)', 'wk-seller-badge'); ?></th>
						<th><?php esc_html_e('Badge Description', 'wk-seller-badge'); ?></th>
					</tr>
					<?php
                    foreach ($buser as $key) {
                        $badge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where id='$key->b_id' and status ='1'");

                        foreach ($badge as $key3) {
                            echo '<tr role="row"> <td>';
                            echo $key3->b_name;
                            echo '</td> <td>'; ?>
								<img src='<?php echo wp_get_attachment_url($key3->tumbnail); ?>'  style="width:100px;">
								<?php
                                echo '</td><td>'.$key3->b_des.'</td></tr>';
                        }
                    } ?>
				</table>
			</div>
			<?php
        }
    }
