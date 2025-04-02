<?php
namespace Ericc70\Module\Googlereviews\Site\Service;

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
        if ($this->logLevel === 'none') {
            return;
        }

        $logLevel = match ($level) {
            'debug' => Log::DEBUG,
            'info' => Log::INFO,
            'warning' => Log::WARNING,
            'error' => Log::ERROR,
            default => Log::ERROR,
        };

        Log::add($message, $logLevel, 'mod_google_reviews');
    }

    protected function handleError(\Exception $e): array
    {
        $errorMessage = $e->getMessage();
        $this->logError($errorMessage, $this->logLevel);

        if ($this->errorDisplay === 'debug') {
            return [
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ];
        }

        return ['error' => $this->getUserFriendlyError($e)];
    }

    protected function getUserFriendlyError(\Exception $e): string
    {
        return match (true) {
            $e instanceof \InvalidArgumentException => self::ERROR_API_KEY,
            str_contains($e->getMessage(), 'INVALID_REQUEST') => self::ERROR_PLACE_ID,
            str_contains($e->getMessage(), 'OVER_QUERY_LIMIT') => self::ERROR_RATE_LIMIT,
            str_contains($e->getMessage(), 'REQUEST_DENIED') => self::ERROR_API_KEY,
            str_contains($e->getMessage(), 'Erreur HTTP') => self::ERROR_NETWORK,
            default => self::ERROR_GENERIC
        };
    }

    protected function decodeJson(string $json): array
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError('Erreur de décodage JSON: ' . json_last_error_msg(), 'error');
            throw new \RuntimeException('Erreur lors du traitement de la réponse');
        }
        return $data;
    }
}