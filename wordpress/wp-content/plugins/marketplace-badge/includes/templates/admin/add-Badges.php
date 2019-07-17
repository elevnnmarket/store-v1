<?php
/**
 * Add badge admin side.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['submit'])) {
    $savebages = new MP_SAVE_BADGES();
    if ($savebages->check($_POST) == 1) {
        if ($_POST['action'] == 'edit') {
            $savebages->b_name = $_POST['b_name'];
            $savebages->b_des = $_POST['b_des'];
            $savebages->rank = intval($_POST['rank']);
            $savebages->image = $_POST['upload-img-id'];
            $savebages->status = intval($_POST['status']);
            $savebages->id = intval($_GET['id']);
            $savebages->update_badge();
        } else {
            $savebages->add_new_badge();
        }
    }

    if (!empty($savebages->get_sucess())) {
        ?>
</head>
<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
    <p>
        <strong>
            <?php echo $savebages->get_sucess(); ?>
        </strong>
    </p>
</div>
<?php
    } elseif (!empty($savebages->get_error())) {
        ?>
<div class="error">
    <p>
        <strong>
            <?php echo $savebages->get_error(); ?>
        </strong>
    </p>
</div>
<?php
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $b_obj = new MP_Get_Seller_Badge();
    $binfo = $b_obj->mp_get_badge_details($_GET['id']);
} else {
    $binfo = array(
        'b_name' => '',
        'b_des' => '',
        'rank' => '',
        'tumbnail' => '',
        'status' => '',
    );
}
?>
<div class="wrap">
    <?php
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        ?>
    <h2><?php esc_html_e('UPDATE BADGE', 'wk-seller-badge'); ?></h2>
    <?php
    } else {
        ?>
    <h2><?php esc_html_e('ADD BADGE', 'wk-seller-badge'); ?></h2>
    <?php
    }
?>

    <form method="post" enctype="multipart/form-data" novalidate="novalidate">
        <table class="form-table">
            <tr>
                <th scope="row"> <label for="b_name"> <?php esc_html_e('Badge Name', 'wk-seller-badge'); ?>
                </th>
                <td style="padding: 10px;"><input type="text" name="b_name" id="b_name" value='<?php if (isset($binfo['b_name'])) {
    echo $binfo['b_name'];
} ?>'>
                    </label>

                </td>
            </tr>
            <tr>
                <th scope="row"> <label for="b_des"> <?php esc_html_e('Badge Description', 'wk-seller-badge'); ?>
                </th>
                <td style="padding: 10px;"><textarea name="b_des" id="b_des" cols="23"><?php if (isset($binfo['b_des'])) {
    echo $binfo['b_des'];
} ?></textarea>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"> <label for="rank"> <?php esc_html_e('Rank', 'wk-seller-badge'); ?>
                </th>
                <td style="padding: 10px;"><input type="number" min="1" step="1" name="rank" id="rank" value='<?php if (isset($binfo['rank'])) {
    echo $binfo['rank'];
} ?>'>
                    </label>
                </td>
            </tr>
            <tr>
            <tr>
                <th scope="row"> <label for="status"> <?php esc_html_e('Status', 'wk-seller-badge'); ?>
                </th>
                <td style="padding: 10px;">
                    <select name="status">
                        <option value="1" <?php if (isset($binfo['status'])) {
    if ($binfo['status'] == 'Disable') {
        echo ' selected="selected"';
    }
} ?>><?php esc_html_e('Enable', 'wk-seller-badge'); ?></option>
                        <option value="0" <?php if (isset($binfo['status'])) {
    if ($binfo['status'] == 'Enable') {
        echo ' selected="selected"';
    }
} ?>><?php esc_html_e('Disable', 'wk-seller-badge'); ?></option>
                    </select>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"> <label for="image_url"> <?php esc_html_e('Badge', 'wk-seller-badge'); ?> </label>
                </th>
                <td style="padding: 10px;">
                    <img src='<?php if (isset($binfo['tumbnail'])) {
    echo wp_get_attachment_url($binfo['tumbnail']);
} ?>' id="image_url2" style="width:100px;">
                    <p id="image_up_error"> </p>
                    <input type="hidden" name="image_url2" id="image_url_2" class="regular-text" value='<?php if (isset($binfo['tumbnail'])) {
    echo wp_get_attachment_url($binfo['tumbnail']);
} ?>'>
                    <input type="hidden" name="upload-img-id" id="upload-img-id" value="<?php echo $binfo['tumbnail']; ?>">
                    <input type="button" name="upload-btn" id="upload-btn-2" class="button-secondary" value="<?php esc_html_e('Upload Badge', 'wk-seller-badge'); ?>">
                    </label>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;">
                    <input type="hidden" name="_wpnonce" value="<?php echo  wp_create_nonce(); ?>">
                    <input type='hidden' name='action' value='<?php if (isset($_GET['action'])) {
    echo $_GET['action'];
} ?>'>
                    <input type="hidden" name='id' value='<?php if (isset($_GET['id'])) {
    echo $_GET['id'];
} ?>'>
                    <?php
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        ?>
                    <p class="submit"> <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Update Badge', 'wk-seller-badge'); ?>"> </p>
                    <?php
                    } else {
                        ?>
                    <p class="submit"> <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Add Badge', 'wk-seller-badge'); ?>"> </p>
                    <?php
                    }
                ?>
                </td>
            </tr>

        </table>
    </form>
</div> 