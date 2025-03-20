<?php
namespace Joomla\Module\GoogleReviews;

use Joomla\CMS\Log\Log;

abstract class ErrorHandler
{
    protected string $errorDisplay = 'user_friendly';
    protected string $logLevel = 'error';

    // Constantes pour les messages d'erreur
    protected const ERROR_API_KEY = 'Clé API Google invalide ou manquante';
    protected const ERROR_PLACE_ID = 'ID du lieu Google invalide ou manquant';
    protected const ERROR_NO_REVIEWS = 'Aucun avis disponible pour ce lieu';
    protected const ERROR_RATE_LIMIT = 'Limite de requêtes API Google atteinte';
    protected const ERROR_NETWORK = 'Erreur de connexion au service Google';
    protected const ERROR_GENERIC = 'Une erreur est survenue lors de la récupération des avis';

    protected function logError(string $message, string $level): void
    {
        if ($this->logLevel === 'debug' || 
            ($this->logLevel === 'warning' && in_array($level, ['warning', 'error'])) ||
            ($this->logLevel === 'error' && $level === 'error')) {
            
            Log::add($message, constant('Joomla\\CMS\\Log\\Log::' . strtoupper($level)), 'mod_google_reviews');
        }
    }

    protected function getErrorMessage(\Exception $e): string
    {
        $message = $e->getMessage();
        
        if (strpos($message, 'API key') !== false) {
            return self::ERROR_API_KEY;
        } elseif (strpos($message, 'place_id') !== false) {
            return self::ERROR_PLACE_ID;
        } elseif (strpos($message, 'no reviews') !== false) {
            return self::ERROR_NO_REVIEWS;
        } elseif (strpos($message, 'rate limit') !== false) {
            return self::ERROR_RATE_LIMIT;
        } elseif (strpos($message, 'network') !== false || strpos($message, 'connection') !== false) {
            return self::ERROR_NETWORK;
        }
        
        return self::ERROR_GENERIC;
    }

    protected function handleError(\Exception $e): array
    {
        $errorMessage = $this->getErrorMessage($e);
        
        switch ($this->errorDisplay) {
            case 'none':
                return ['error' => ''];
            case 'detailed':
                return ['error' => $e->getMessage()];
            case 'user_friendly':
            default:
                return ['error' => $errorMessage];
        }
    }

    protected function decodeJson(string $json): array
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logError('Erreur lors du décodage de la réponse JSON: ' . $e->getMessage(), 'error');
            throw new \RuntimeException('Erreur lors du décodage de la réponse JSON: ' . $e->getMessage());
        }
    }
}