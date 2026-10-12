<?php

namespace Concrete\Package\NotificationBannerKit\Controller\SinglePage\Dashboard\NotificationBanner;

use File;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Http\ResponseFactoryInterface;
use NotificationBannerKit\Entity\NotificationContent;
use Concrete\Core\Page\Controller\DashboardPageController;
use NotificationBannerKit\Entity\Notification as NotificationEntity;
use NotificationBannerKit\Search\ItemList\Notifications as NotificationItemList;
use Concrete\Package\NotificationBannerKit\Controller\Search\Notifications as SearchController;

class Notifications extends DashboardPageController
{
    protected $helpers = [
        'form',
        'concrete/ui',
        'concrete/asset_library',
        'form/page_selector',
        'form/date_time',
    ];
    protected $locales;
    protected $styles = [
        'banner' => 'Banner',
        'popup' => 'Popup',
    ];
    protected $displayScopes = [
        'all' => 'All Pages',
        'home' => 'Homepage Only',
    ];

    /**
     * Styles that don't already have a Notification (excluding $excludeId, for editing).
     */
    protected function getAvailableStyles($excludeId = null)
    {
        $available = [];

        foreach ($this->styles as $handle => $label) {
            if (!$this->styleExists($handle, $excludeId)) {
                $available[$handle] = $label;
            }
        }

        return $available;
    }

    protected function styleExists(string $style, $excludeId = null)
    {
        $nl = new NotificationItemList();
        $nl->filterByStyle($style);

        foreach ($nl->getResults() as $n) {
            if ($excludeId === null || $n->getID() != $excludeId) {
                return true;
            }
        }

        return false;
    }

    protected function validate($args)
    {
        $vstrings = $this->app->make('helper/validation/strings');

        if (!$vstrings->notempty($args['name']['en'])) {
            $this->error->add(t('Please enter a name in English'));
        }

        if (!$vstrings->notempty($args['content']['en'])) {
            $this->error->add(t('Please enter some content in English'));
        }

        if (empty($args['style']) || !isset($this->styles[$args['style']])) {
            $this->error->add(t('Please choose a notification style'));
        } else {
            $excludeId = !empty($args['id']) ? $args['id'] : null;
            if ($this->styleExists($args['style'], $excludeId)) {
                $label = $this->styles[$args['style']];
                $this->error->add(t('A %s notification already exists. Only one banner and one popup notification are allowed.', $label));
            }
        }

        $dateTimeHelper = $this->app->make('helper/form/date_time');
        $startDate = $dateTimeHelper->translate('startDate', $args, true);
        $endDate = $dateTimeHelper->translate('endDate', $args, true);

        if ($startDate && $endDate && $startDate > $endDate) {
            $this->error->add(t('The start date must be before the end date'));
        }
    }

    public function on_start()
    {
        parent::on_start();

        $site = $this->app->make('site')->getActiveSiteForEditing();
        $this->locales = $site->getLocales();
        $this->set('locales', $this->locales);
        $this->set('styles', $this->styles);
        $this->set('displayScopes', $this->displayScopes);
    }

    public function view()
    {
        $reset = false;

        if ($this->request->isPost()) {
            if (!$this->token->validate('notification-search')) {
                $this->error->add($this->token->getErrorMessage());
            } else {
                $reset = true;
            }
        }

        $search = $this->app->make(SearchController::class);
        $search->search($reset);

        $result = $search->getSearchResultObject();

        $this->set('result', $result);
        $this->set('items', $result->getItems());
        $this->set('pagination', $result->getPaginationHTML());

        $allowed_num_results = array_combine(
            $search->getAllowedPaginationSizes(),
            $search->getAllowedPaginationSizes(),
        );

        $params = $search->getStickyRequest()->getSearchRequest();
        $this->set('name', isset($params['name']) ? $params['name'] : '');

        if (isset($params['num_results']) && isset($allowed_num_results[$params['num_results']])) {
            $num_results = (int) $params['num_results'];
        } else {
            $num_results = $search->getDefaultPaginationSize();
        }

        $this->set('num_results', $num_results);
        $this->set('params', $params);
        $this->set('allowed_num_results', $allowed_num_results);
        $this->set('canAddNotification', count($this->getAvailableStyles()) > 0);
    }

    public function add()
    {
        $this->requireAsset('core/sitemap');
        $this->set('styles', $this->getAvailableStyles());
    }

    public function details($id = false)
    {
        if (!$id) {
            $this->buildRedirect('/dashboard/notification_banner/notifications/')->send();
        }

        $entity = NotificationEntity::getByID($id);

        if (!$entity) {
            $this->buildRedirect('/dashboard/notification_banner/notifications/')->send();
        }

        $this->set('entity', $entity);
        $this->set('styles', $this->getAvailableStyles($entity->getID()));
    }

    public function save()
    {
        if ($this->post()) {
            $post = $this->post();

            if (!$this->token->validate('submit')) {
                $this->error->add($this->token->getErrorMessage());
            }

            $this->validate($post);

            if (!$this->error->has()) {
                if (isset($post['id'])) {
                    $entity = NotificationEntity::getByID($post['id']);
                }

                if (!isset($entity)) {
                    $entity = new NotificationEntity();
                }

                $entity->setStyle($post['style']);
                $entity->setActive(isset($post['active']));
                $entity->setDisplayScope(isset($this->displayScopes[$post['displayScope'] ?? '']) ? $post['displayScope'] : 'all');

                $dateTimeHelper = $this->app->make('helper/form/date_time');
                $entity->setStartDate($dateTimeHelper->translate('startDate', $post, true));
                $entity->setEndDate($dateTimeHelper->translate('endDate', $post, true));

                foreach ($this->locales as $locale) {
                    $lang = $locale->getLanguage();

                    $data = $entity->getBanner($lang);
                    if (isset($post['content'][$lang])) {
                        $post['content'][$lang] = LinkAbstractor::translateTo($post['content'][$lang]);
                    }

                    if (!$data) {
                        $data = new NotificationContent();
                        $data->setLanguage($lang);
                    }

                    if ($post['style'] == 'popup') {
                        $image = File::getByID($post['image'][$lang]);
                        $data->setImage($image ?? null);
                    }

                    $data->setName($post['name'][$lang]);
                    $data->setContent($post['content'][$lang]);
                    $data->setLinkText($post['linkText'][$lang]);
                    $data->setLinkCID($post['linkCID'][$lang]);

                    $this->entityManager->persist($data);

                    $entity->addData($data);
                }

                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $this->flash('success', t('Notification Saved.'));

                return $this->buildRedirect('/dashboard/notification_banner/notifications/')->send();

            }
            $this->set('formContent', $post);
            $this->set('styles', $this->getAvailableStyles(!empty($post['id']) ? $post['id'] : null));

        }
    }

    public function delete($id = false)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        if (!$this->token->validate('delete-notification-id-' . $id)) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $entity = NotificationEntity::getByID($id);

            if ($entity && $entity->getID() > 0) {
                // Set deleted message
                $this->flash('success', t('Notification Deleted.'));

                $data = $entity->getData();
                foreach ($data as $d) {
                    $this->entityManager->remove($d);
                }

                // run the delete.
                $this->entityManager->remove($entity);
                $this->entityManager->flush();

                return $rf->json(true);
            }
            $this->error->add(t('Unable to find the specified Notification details.'));

        }

        return $rf->json($this->error->jsonSerialize());
    }
}
