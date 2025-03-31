<?php
namespace Ericc70\Module\Googlereviews\Site\Dispatcher;

\defined('_JEXEC') or die;

use Ericc70\Module\Googlereviews\Site\Helper\GooglereviewsHelper;
use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Helper\ModuleHelper;

class Dispatcher implements DispatcherInterface
{
    public function dispatch()
    {
        $hello = GooglereviewsHelper::getReviews('hello');

        require ModuleHelper::getLayoutPath('mod_google_reviews');
    }
}