<div id="nb-notification__<?php echo (int) $id; ?>" class="nb-notification notification-popup" data-style="<?php echo h($style); ?>" data-banner-id="<?php echo (int) $id; ?>">
    <div class="notification-popup__backdrop"></div>

    <div class="notification-popup__wrapper" role="dialog" aria-modal="true" aria-labelledby="nb-notification__<?php echo (int) $id; ?>__title">
        <a href="#nb-notification__<?php echo (int) $id; ?>" class="close-nb-notification" aria-label="<?php echo t('Close notification'); ?>"></a>

        <div class="container">
            <div class="row align-items-center">
                <?php if ($notification->getImage()) { ?>
                    <div class="col-12 col-lg-7 order-1 order-lg-2 mb-3 mb-lg-0">
                        <?php
                            \View::element('picture', [
                                'f' => $notification->getImage(),
                                'classes' => ['img-fluid'],
                                // The "picture" element is supplied by the active theme, so pass a value that is safe whether or not it escapes.
                                'altText' => preg_replace('/[<>"\'&`]/', '', strip_tags((string) $notification->getImage()->getTitle())),
                            ]);
                    ?>
                    </div>
                <?php } ?>

                <div class="col-12 col-lg order-2 order-lg-1">
                    <h4 class="notification-popup__title" id="nb-notification__<?php echo (int) $id; ?>__title"><?php echo h($notification->getName()); ?></h4>
                    <div class="notification-popup__content"><?php echo $notification->getContent(false); ?></div>
                    <?php if ($notification->getPage()) { ?>
                        <div class="notification-popup__button--container">
                            <a href="<?php echo h($notification->getPage()->getCollectionLink()); ?>" class="btn btn-secondary">
                                <?php echo h(($notification->getLinkText() !== '') ? $notification->getLinkText() : $notification->getPage()->getCollectionName()); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>