<?php
namespace Ericc70\Module\Googlereviews\Site\Service;

use Ericc70\Module\Googlereviews\Site\Service\ErrorHandler;
use Ericc70\Module\Googlereviews\Site\Service\ReviewService;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;

class GoogleReviewService extends ErrorHandler implements ReviewService
{
    private CallbackController $cache;
    private HttpFactory $httpFactory;
    private array $config;

    public function __construct(
        CacheControllerFactoryInterface $cacheFactory,
        HttpFactory $httpFactory
    ) {
        $this->cache = $cacheFactory->createCacheController('callback', ['defaultgroup' => 'mod_googlereviews']);
        $this->httpFactory = $httpFactory;
    }

    public function getReviews(array $config) :array
    {
        $this->config = $config;
        $this->errorDisplay = $config['error_display'] ?? 'user_friendly';
        $this->logLevel = $config['error_log_level'] ?? 'error';

        $cacheId = md5(serialize($config));
        $cacheEnabled = $config['cache_enabled'] ?? true;
        $cacheTime = $config['cache_time'] ?? 3600;

        try {
            if (!$cacheEnabled) {
                return $this->fetchAndProcessReviews($config);
            }

            return $this->cache->get(
                function () use ($config) {
                    return $this->fetchAndProcessReviews($config);
                },
                [],
                $cacheId,
                false,
                $cacheTime
            );
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    
    }

    private function fetchAndProcessReviews(array $config): array
    {
        var_dump($config);
        if ($config['test_mode'] == 0) {
            return $this->getTestReviews($config);
        }

        if (empty($config['apiKey']) || empty($config['placeId'])) {
            $this->logError('Clé API et/ou ID de lieu manquant', 'warning');
            throw new \InvalidArgumentException('Clé API et/ou ID de lieu requis');
        }

        $url = sprintf(
            'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&fields=reviews&key=%s',
            $config['placeId'],
            $config['apiKey']
        );

        $response = $this->httpFactory->getHttp()->get($url);

        if ($response->code !== 200) {
            $this->logError("Erreur HTTP: {$response->code}", 'error');
            throw new \RuntimeException("Erreur HTTP: {$response->code}");
        }

        $data = $this->decodeJson($response->body);

        if (isset($data['status']) && $data['status'] !== 'OK') {
            $errorMessage = $data['error_message'] ?? 'Erreur inconnue';
            $this->logError('Erreur de l\'API Google: ' . $errorMessage, 'error');
            throw new \RuntimeException('Erreur de l\'API Google: ' . $errorMessage);
        }

        if (!isset($data['result']['reviews'])) {
            return ['error' => 'Aucun avis disponible pour ce lieu.'];
        }

        return array_slice($data['result']['reviews'], 0, $config['maxReviews'] ?? 5);
    }

    private function getTestReviews(array $config): array
    {
        $maxReviews = $config['maxReviews'] ?? 5;
        $reviews = [];
        
        $authors = ['Jean Dupont', 'Marie Martin', 'Pierre Bernard', 'Sophie Petit', 'Lucas Dubois'];
        $comments = [
            'Excellent service, je recommande vivement !',
            'Très satisfait de ma visite, personnel accueillant.',
            'Bonne expérience globale, quelques points à améliorer.',
            'Service rapide et efficace, merci !',
            'Équipe professionnelle et sympathique.'
        ];
        
        for ($i = 0; $i < $maxReviews; $i++) {
            $reviews[] = [
                'author_name' => $authors[$i],
                'author_url' => 'https://www.google.com/maps/contrib/' . rand(100000000, 999999999),
                'rating' => rand(3, 5),
                'text' => $comments[$i],
                'relative_time_description' => 'il y a ' . rand(1, 30) . ' jours',
                'time' => time() - (rand(1, 30) * 86400),
                'profile_photo_url' => 'https://example.com/placeholder.jpg',
                'language' => 'fr'
            ];
        }
        
        return ['result' => ['reviews' => $reviews]];
    }
}