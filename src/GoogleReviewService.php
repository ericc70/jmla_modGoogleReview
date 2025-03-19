<?php
namespace Joomla\Module\GoogleReviews;

use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\CallbackController;

class GoogleReviewService implements ReviewService
{
    private CallbackController $cache;
    private HttpFactory $httpFactory;

    public function __construct(
        CacheControllerFactoryInterface $cacheFactory,
        HttpFactory $httpFactory
    ) {
        $this->cache = $cacheFactory->createCacheController('callback', ['defaultgroup' => 'mod_googlereviews']);
        $this->httpFactory = $httpFactory;
    }

    public function getReviews(array $config): array
    {
        $cacheId = md5(serialize($config));

        try {
            return $this->cache->get(
                function () use ($config) {
                    return $this->fetchAndProcessReviews($config);
                },
                [],
                $cacheId,
                false,
                3600
            );
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function fetchAndProcessReviews(array $config): array
    {
        if (empty($config['apiKey']) || empty($config['placeId'])) {
            throw new \InvalidArgumentException('Clé API et/ou ID de lieu requis');
        }

        $url = sprintf(
            'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&fields=reviews&key=%s',
            $config['placeId'],
            $config['apiKey']
        );

        $response = $this->httpFactory->getHttp()->get($url);

        if ($response->code !== 200) {
            throw new \RuntimeException("Erreur HTTP: {$response->code}");
        }

        $data = $this->decodeJson($response->body);

        if (isset($data['status']) && $data['status'] !== 'OK') {
            throw new \RuntimeException('Erreur de l\'API Google: ' . ($data['error_message'] ?? 'Erreur inconnue'));
        }

        if (!isset($data['result']['reviews'])) {
            return ['error' => 'Aucun avis disponible pour ce lieu.'];
        }

        return array_slice($data['result']['reviews'], 0, $config['maxReviews'] ?? 5);
    }

    private function decodeJson(string $json): array
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Erreur lors du décodage de la réponse JSON: ' . $e->getMessage());
        }
    }
}