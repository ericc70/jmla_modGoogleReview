<?php
namespace Ericc70\Module\Googlereviews\Site\Dispatcher;

\defined('_JEXEC') or die;

use Ericc70\Module\Googlereviews\Site\Helper\GooglereviewsHelper;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\Input\Input;

class Dispatcher implements DispatcherInterface
{
    protected $module;
    protected $app;
    protected $input;

    public function __construct(\stdClass $module, CMSApplicationInterface $app, ?Input $input = null)
    {        
        $this->module = $module;
        $this->app = $app;
        $this->input = $input ?: $app->getInput();
    }

    public function dispatch()
    {
        // Register and load the module's CSS
      //  $this->app->getDocument()->getWebAssetManager()->useStyle('mod_google_reviews.default');

        $params = new Registry($this->module->params);
        $helper = new GooglereviewsHelper();
        $reviews = $helper->getReviews();

        // Make the application available for the template
         $app = $this->app;
        
        require ModuleHelper::getLayoutPath('mod_google_reviews', 'default');
    }
}