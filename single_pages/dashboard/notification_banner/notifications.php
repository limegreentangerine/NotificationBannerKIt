<?php defined('C5_EXECUTE') or die('Access Denied.');

if (in_array($controller->getTask(), ['add', 'details', 'save'])) {
    $formData = [
        'id' => (isset($entity)) ? $entity->getID() : ((isset($formContent) && isset($formContent['id'])) ? $formContent['id'] : ''),
        'style' => (isset($entity)) ? $entity->getStyle() : ((isset($formContent) && isset($formContent['style'])) ? $formContent['style'] : ''),
        'active' => (isset($entity)) ? $entity->isActive() : ((isset($formContent)) ? isset($formContent['active']) : true),
        'startDate' => (isset($entity)) ? $entity->getStartDate() : ((isset($formContent) && isset($formContent['startDate'])) ? $formContent['startDate'] : ''),
        'endDate' => (isset($entity)) ? $entity->getEndDate() : ((isset($formContent) && isset($formContent['endDate'])) ? $formContent['endDate'] : ''),
        'displayScope' => (isset($entity)) ? $entity->getDisplayScope() : ((isset($formContent) && isset($formContent['displayScope'])) ? $formContent['displayScope'] : 'all'),
    ];

    foreach ($locales as $locale) {
        $formData[$locale->getLanguage()] = [
            'name' => (isset($entity)) ? $entity->getBanner($locale->getLanguage())->getName() : ((isset($formContent) && isset($formContent['name'])) ? $formContent['name'][$locale->getLanguage()] : ''),
            'image' => (isset($entity)) ? $entity->getBanner($locale->getLanguage())->getImage() : ((isset($formContent) && isset($formContent['image'])) ? $formContent['image'][$locale->getLanguage()] : ''),
            'content' => (isset($entity)) ? $entity->getBanner($locale->getLanguage())->getContent(false, true) : ((isset($formContent) && isset($formContent['content'])) ? $formContent['content'][$locale->getLanguage()] : ''),
            'linkText' => (isset($entity)) ? $entity->getBanner($locale->getLanguage())->getLinkText() : ((isset($formContent) && isset($formContent['linkText'])) ? $formContent['linkText'][$locale->getLanguage()] : ''),
            'linkCID' => (isset($entity)) ? $entity->getBanner($locale->getLanguage())->getLinkCID() : ((isset($formContent) && isset($formContent['linkCID'])) ? $formContent['linkCID'][$locale->getLanguage()] : ''),
        ];
    }

    View::element(
        'dashboard/notifications/details',
        [
            'token' => $token,
            'form' => $form,
            'entity' => $entity ?? null,
            'locales' => $locales,
            'styles' => $styles,
            'displayScopes' => $displayScopes,
            'h' => [
                'al' => $concrete_asset_library,
                'ps' => $form_page_selector,
                'dt' => $form_date_time,
            ],
            'v' => $formData,
        ],
        'notification_banner_kit',
    );
} else {
    View::element(
        'dashboard/notifications/all',
        [
            'token' => $token,
            'params' => $params,
            'items' => $items,
            'result' => $result,
            'pagination' => $pagination,
            'canAddNotification' => $canAddNotification ?? false,
        ],
        'notification_banner_kit',
    );
}
