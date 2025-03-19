<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\GoogleReviews\GoogleReviewService;

// Créer le service de récupération des avis
$container = Factory::getContainer();
$reviewService = new GoogleReviewService(
    $container->get('cache.controller.factory'),
    $container->get('http.factory')
);

// Récupérer les avis via le service
$config = [
    'apiKey' => $params->get('api_key'),
    'placeId' => $params->get('place_id'),
    'maxReviews' => (int) $params->get('max_reviews', 5)
];

$result = $reviewService->getReviews($config);

// Vérifier s'il y a une erreur
if (isset($result['error'])) {
    echo '<p>' . htmlspecialchars($result['error']) . '</p>';
    return;
}

$reviews = $result;

// Inclure le template
require ModuleHelper::getLayoutPath('mod_google_reviews', $params->get('layout', 'default'));