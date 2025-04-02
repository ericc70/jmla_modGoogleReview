<?php
namespace Ericc70\Module\Googlereviews\Site\Service;

interface ReviewService
{
    /**
     * Récupère les avis pour un lieu donné.
     *
     * @param array $config Configuration pour la récupération des avis.
     * @return array Tableau des avis ou un tableau d'erreurs.
     * @throws \Exception Si une erreur survient lors de la récupération des avis.
     */
    public  function getReviews(array $config): array;
}