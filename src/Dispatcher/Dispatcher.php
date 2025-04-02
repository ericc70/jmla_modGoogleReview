<?php
namespace Ericc70\Module\Googlereviews\Site\Dispatcher;

\defined('_JEXEC') or die;

use Ericc70\Module\Googlereviews\Site\Helper\GooglereviewsHelper;
use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

class Dispatcher implements DispatcherInterface
{
    public function dispatch()
    {
        $module = ModuleHelper::getModule('mod_google_reviews');
        $params = new Registry($module->params);
        $helper = new GooglereviewsHelper();
        $reviews = $helper->getReviews();

        require ModuleHelper::getLayoutPath('mod_google_reviews', 'default');
    }
}