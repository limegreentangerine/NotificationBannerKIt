<div id="nb-notification__<?php echo (int) $id; ?>" class="nb-notification notification-banner" data-style="<?php echo h($style); ?>" data-banner-id="<?php echo (int) $id; ?>">
    <div class="notification-banner__wrapper py-2 px-4">
        <div class="notification-banner__content">
            <?php echo $notification->getContent(false); ?>
            <?php if ($notification->getPage()) { ?>
                <a href="<?php echo h($notification->getPage()->getCollectionLink()); ?>" class="notification-banner__link btn ms-2"><?php echo h($notification->getLinkText()) ?? h($notification->getPage()->getCollectionName()); ?></a>
            <?php } ?>
        </div>

        <?php if ($dismissable) { ?>
            <a href="#nb-notification__<?php echo (int) $id; ?>" data-id="<?php echo (int) $id; ?>" class="notification-banner__close close-nb-notification" aria-label="<?php echo t('Close notification'); ?>"></a>
        <?php } ?>
    </div>
</div>