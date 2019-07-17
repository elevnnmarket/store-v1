<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'RMA_Settings' ) )
{

    /**
     *
     */
    class RMA_Settings
    {

      function __construct()
      {

        add_action( 'mp_rma_settings', array( $this, 'mp_rma_settings' ) );

      }

      function mp_rma_settings() {

          ?>

          <form action="options.php" method="post">

              <?php settings_fields('mp_rma_settings_group'); ?>
              <table class="form-table">
                  <tbody>

                      <tr valign="top">
                          <th scope="row" class="titledesc">
                              <label for="rma_enable">RMA Status</label>
            	            </th>
                          <td class="forminp">
                              <select name="mp_rma_status" id="rma_enable" style="min-width:350px;">
                                  <option value="">-- Select --</option>
                                  <option value="enabled" <?php if (get_option('mp_rma_status') == 'enabled') echo 'selected'; ?>>Enabled</option>
                                  <option value="disabled" <?php if (get_option('mp_rma_status') == 'disabled') echo 'selected'; ?>>Disabled</option>
                              </select>
                          </td>
                      </tr>

                      <tr valign="top">
                          <th scope="row" class="titledesc">
                              <label for="rma_time">RMA Time</label>
            	            </th>
                          <td class="forminp">
                              <input type="text" name="mp_rma_time" id="rma_time" value="<?php echo get_option('mp_rma_time'); ?>" style="min-width:350px;" />
                              <p class="description">You can add Time limit for customer, only less than these days customer can generate RMA for any order.</p>
                          </td>
                      </tr>

                      <tr valign="top">
                          <th scope="row" class="titledesc">
                              <label for="rma_statuses">Order Status for RMA</label>
                          </th>
                          <td class="forminp">
                              <select name="mp_rma_order_statuses[]" id="rma_statuses" multiple="true" style="min-width:350px;">
                                  <?php foreach ( wc_get_order_statuses() as $key => $value ): ?>
                                      <option value="<?php echo $key; ?>" <?php if (get_option('mp_rma_order_statuses')) { foreach (get_option('mp_rma_order_statuses') as $k => $val) {
                                        if( $val == $key ) echo 'selected';
                                      } }  ?>><?php echo $value; ?></option>
                                  <?php endforeach; ?>
                              </select>
                              <p class="description">Customer can place RMA only for those status of order which is selected here.</p>
                          </td>
                      </tr>

                      <tr valign="top">
                          <th scope="row" class="titledesc">
                              <label for="rma_address">Return Address</label>
            	            </th>
                          <td class="forminp">
                              <textarea name="mp_rma_address" rows="4" id="rma_address" style="min-width:350px;"><?php echo get_option('mp_rma_address'); ?></textarea>
                              <p class="description">Use Comma(,) to seperate.</p>
                              <p class="description">After send Shipping lable to customer this will be your return address for product.</p>
                          </td>
                      </tr>

                      <tr valign="top">
                          <th scope="row" class="titledesc">
                              <label for="rma_policy">RMA Policy</label>
            	            </th>
                          <td class="forminp">
                              <textarea name="mp_rma_policy" rows="4" id="rma_policy" style="min-width:350px;"><?php echo get_option('mp_rma_policy'); ?></textarea>
                              <p class="description">Using this you can add policy which will display to customer at time of Add RMA.</p>
                          </td>
                      </tr>

                  </tbody>
              </table>

              <?php submit_button(); ?>

          </form>
          <?php

      }

    }

    new RMA_Settings();

}
