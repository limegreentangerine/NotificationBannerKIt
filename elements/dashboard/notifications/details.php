<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if ($v['id']) { ?>
    <div class="ccm-dashboard-header-buttons">
        <button data-launch-dialog="delete-dialog" class="btn btn-danger"><?php echo t('Delete'); ?></button>
    </div>

    <div style="display: none" data-dialog="delete-dialog" class="ccm-ui">
        <form data-dialog-form="delete-form" method="POST" action="<?php echo $view->action('delete', (int) $v['id']); ?>">
            <?php echo $token->output('delete-notification-id-' . (int) $v['id']); ?>
            <p><?php echo t('Are you sure you want to permanently delete this notification?'); ?></p>
            <p><strong><?php echo t('WARNING: this operation can not be undone!'); ?></strong></p>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?php echo t('Cancel'); ?></button>
            <button class="btn btn-danger pull-right" data-dialog-action="submit"><?php echo t('Delete'); ?></button>
        </div>
    </div>

    <script>
    $(function() {
        var $dialog = $('div[data-dialog="delete-dialog"]');
        $('[data-launch-dialog="delete-dialog"]').on('click', function(e) {
            e.preventDefault();
            jQuery.fn.dialog.open({
                element: $dialog,
                modal: true,
                width: 420,
                title: <?php echo json_encode(t('Confirm Delete'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
                height: 'auto'
            });
        });
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form === 'delete-form') {
                window.location.href = <?php echo json_encode((string) URL::to('/dashboard/notification_banner/notifications'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            }
        });
    });
    </script>
<?php } ?>

<form method="post" action="<?php echo $view->action('save'); ?>">
    <?php echo $token->output('submit') ?>
    <?php echo ($entity) ? $form->hidden('id', $entity->getID()) : ''; ?>

    <fieldset>
        <legend><?php echo t('Style'); ?></legend>

        <div class="form-group">
            <?php echo $form->label('style', t('Notification Style')); ?>
            <?php echo $form->select('style', $styles, $v['style'] ?? null); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('displayScope', t('Show On')); ?>
            <?php echo $form->select('displayScope', $displayScopes, $v['displayScope'] ?? 'all'); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Status'); ?></legend>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="active" name="active" class="form-check-input" value="1" <?php echo (!empty($v['active'])) ? 'checked' : ''; ?> />
                <label for="active" class="form-check-label"><?php echo t('Active'); ?></label>
            </div>
            <div class="help-block"><?php echo t('Only active notifications can be selected on the Settings page.'); ?></div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Scheduling'); ?></legend>

        <div class="form-group">
            <?php echo $form->label('startDate', t('Start Date/Time')); ?>
            <?php echo $h['dt']->datetime('startDate', $v['startDate'] ?? ''); ?>
            <div class="help-block"><?php echo t('Leave blank to display immediately.'); ?></div>
        </div>

        <div class="form-group">
            <?php echo $form->label('endDate', t('End Date/Time')); ?>
            <?php echo $h['dt']->datetime('endDate', $v['endDate'] ?? ''); ?>
            <div class="help-block"><?php echo t('Leave blank to display indefinitely.'); ?></div>
        </div>
    </fieldset>

    <?php foreach ($locales as $locale) { ?>
        <fieldset>
            <legend><?php echo h($locale->getLanguageText()); ?></legend>

            <div class="form-group">
                <?php echo $form->label('name[' . $locale->getLanguage() . ']', t('Name (%s)', h($locale->getLanguageText()))); ?>
                <div class="float-end">
                    <span class="text-muted small"><?php echo t('Required'); ?></span>
                </div>
                <?php echo $form->text('name[' . $locale->getLanguage() . ']', ((isset($v) && isset($v[$locale->getLanguage()]['name'])) ? $v[$locale->getLanguage()]['name'] : '')); ?>
            </div>

            <div class="form-group <?php echo (isset($v) && $v['style'] == 'popup') ? '' : 'd-none'; ?>" data-type="popup">
                <?php echo $form->label('image[' . $locale->getLanguage() . ']', t('Image (%s)', h($locale->getLanguageText()))); ?>
                <?php echo $h['al']->image('image[' . $locale->getLanguage() . ']', 'image[' . $locale->getLanguage() . ']', t('Choose Image'), ((isset($v) && isset($v[$locale->getLanguage()]['image'])) ? $v[$locale->getLanguage()]['image'] : false)); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('content[' . $locale->getLanguage() . ']', t('Content (%s)', h($locale->getLanguageText()))); ?>
                <div class="float-end">
                    <span class="text-muted small"><?php echo t('Required'); ?></span>
                </div>
                <?php
                    $editor = Core::make('editor');
        $editor->setAllowFileManager(false);
        echo $editor->outputStandardEditor('content[' . $locale->getLanguage() . ']', ((isset($v) && isset($v[$locale->getLanguage()]['content'])) ? $v[$locale->getLanguage()]['content'] : ''));
        ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('linkCID[' . $locale->getLanguage() . ']', t('Linked Page (%s)', h($locale->getLanguageText()))); ?>
                <?php echo $h['ps']->selectPage('linkCID[' . $locale->getLanguage() . ']', ((isset($v) && isset($v[$locale->getLanguage()]['linkCID'])) ? $v[$locale->getLanguage()]['linkCID'] : '')); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('linkText[' . $locale->getLanguage() . ']', t('Link Text (%s)', h($locale->getLanguageText()))); ?>
                <?php echo $form->text('linkText[' . $locale->getLanguage() . ']', ((isset($v) && isset($v[$locale->getLanguage()]['linkText'])) ? $v[$locale->getLanguage()]['linkText'] : '')); ?>
            </div>
        </fieldset>
    <?php } ?>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo URL::to('/dashboard/notification_banner/notifications'); ?>" class="btn btn-secondary float-start"><?php echo t('Cancel'); ?></a>
            <?php echo $form->submit('save', ($entity) ? t('Update Notification') : t('Save Notification'), ['class' => 'btn btn-primary float-end']); ?>
        </div>
    </div>
</form>

<script>
    $(function() {
        const styleSelect = $('select[name=style]');
        const popupFieldWrappers = $('div[data-type=popup]');
        styleSelect.on('change', function() {
            const value = styleSelect.val();
            if (value === 'popup') {
                popupFieldWrappers.removeClass('d-none');
            } else {
                popupFieldWrappers.addClass('d-none');
            }
        });
    });
</script>