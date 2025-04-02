<?php

namespace Ericc70\Module\Googlereviews\Site\Helper;

\defined('_JEXEC') or die;

use Ericc70\Module\Googlereviews\Site\Service\GoogleReviewService;
use Joomla\CMS\Factory;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Http\HttpFactory;
use Joomla\DI\Container;
use Joomla\CMS\Helper\ModuleHelper;

class GooglereviewsHelper
{
    public static function getReviews()
    {
        $module = ModuleHelper::getModule('mod_google_reviews');
        $params = $module->params;
        $paramsArray = json_decode($params, true) ?: [];
        $reviewService = self::getGoogleReviewService()->getReviews($paramsArray);
        return $reviewService;
    }

    protected static function getGoogleReviewService()
    {
        $container = Factory::getContainer();
        $cacheFactory = $container->get(CacheControllerFactoryInterface::class);
        $container->share(
            HttpFactory::class,
            function (Container $container) {
                return new HttpFactory();
            }
        );
        $httpFactory = $container->get(HttpFactory::class);
        $reviewService = new GoogleReviewService($cacheFactory, $httpFactory);
        return $reviewService;
    }
}