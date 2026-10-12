<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if (!$hasNotifications) { ?>
    <div class="alert alert-info">
        <?php echo t('No notifications have been created yet. <a href="%s">Create a notification</a> before changing these settings.', $view->url('/dashboard/notification_banner/notifications/add')); ?>
    </div>
<?php } ?>

<form method="post" action="<?php echo $view->action('save'); ?>">
    <?php echo $token->output('submit') ?>

    <fieldset <?php echo $hasNotifications ? '' : 'disabled'; ?>>
        <legend><?php echo t('Status'); ?></legend>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="activate" name="activate" class="form-check-input" value="1" <?php echo (isset($formContent) && isset($formContent['activate'])) ? 'checked' : (($pkg && $pkg->getFileConfig()->get('notification_banner.activate') == true) ? 'checked' : ''); ?> />
                <label for="activate" class="form-check-label"><?php echo t('Activate'); ?></label>
            </div>
        </div>
    </fieldset>

    <fieldset <?php echo $hasNotifications ? '' : 'disabled'; ?>>
        <legend><?php echo t('Notification Display'); ?></legend>

        <div class="form-group">
            <?php
                echo $form->label('notification_banner', t('Choose Banner Notification'));
echo $form->select('notification_banner', $bannerNotifications, (isset($formContent)) ? $formContent['notification_banner'] : (($pkg && $pkg->getFileConfig()->get('notification_banner.notification_banner') !== '') ? $pkg->getFileConfig()->get('notification_banner.notification_banner') : ''));
?>
        </div>

        <div class="form-group">
            <?php
    echo $form->label('notification_popup', t('Choose Popup Notification'));
echo $form->select('notification_popup', $popupNotifications, (isset($formContent)) ? $formContent['notification_popup'] : (($pkg && $pkg->getFileConfig()->get('notification_banner.notification_popup') !== '') ? $pkg->getFileConfig()->get('notification_banner.notification_popup') : ''));
?>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="use_custom_css" name="use_custom_css" class="form-check-input" value="1" <?php echo (isset($formContent) && isset($formContent['use_custom_css'])) ? 'checked' : (($pkg && $pkg->getFileConfig()->get('notification_banner.use_custom_css') == true) ? 'checked' : ''); ?> />
                <label for="use_custom_css" class="form-check-label"><?php echo t('Use custom css'); ?></label>
            </div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="can_dismiss" name="can_dismiss" class="form-check-input" value="1" <?php echo (isset($formContent) && isset($formContent['can_dismiss'])) ? 'checked' : (($pkg && $pkg->getFileConfig()->get('notification_banner.can_dismiss') == true) ? 'checked' : ''); ?> />
                <label for="can_dismiss" class="form-check-label"><?php echo t('Dismissable?'); ?></label>
            </div>

            <div class="help-block"><?php echo t('Popup notification types are always dismissable'); ?></div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $form->submit('save', t('Save Settings'), ['class' => 'btn btn-primary float-end'] + ($hasNotifications ? [] : ['disabled' => 'disabled'])); ?>
        </div>
    </div>
</form>