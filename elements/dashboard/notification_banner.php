<div id="nb-notification__<?php echo (int) $id; ?>" class="nb-notification notification-banner" data-style="<?php echo h($style); ?>" data-banner-id="<?php echo (int) $id; ?>">
    <div class="notification-banner__wrapper">
        <div class="notification-banner__content"><?php echo $notification->getContent(false); ?></div>
        <?php if ($notification->getPage()) { ?>
            <a href="<?php echo h($notification->getPage()->getCollectionLink()); ?>" class="notification-banner__link" aria-label="<?php echo h($notification->getPage()->getCollectionName()); ?>"></a>
        <?php } ?>

        <?php if ($dismissable) { ?>
            <a href="#nb-notification__<?php echo (int) $id; ?>" data-id="<?php echo (int) $id; ?>" class="notification-banner__close close-nb-notification" aria-label="<?php echo t('Close notification'); ?>"></a>
        <?php } ?>
    </div>
</div>