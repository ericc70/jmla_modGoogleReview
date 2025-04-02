
<?php

use Joomla\CMS\Uri\Uri;

\defined('_JEXEC') or die;
$document = $app->getDocument();
$wa = $document->getWebAssetManager();
$moduleBasePath = Uri::base() . 'modules/mod_google_reviews/media/';
$wa->registerAndUseStyle('mod_google_reviews', $moduleBasePath . 'css/default.css');
// $wa->getRegistry()->addExtensionRegistryFile('mod_google_reviews');

// $wa->useStyle('mod_google_reviews.default');

// Load the CSS file

if (!empty($reviews)): 

    ?>

    <div class="google-reviews">
        <?php foreach ($reviews['result']['reviews'] as  $review):
            ?>
            <div class="review">
                <div class="review-header">
                    <h3 class="review-author">
                        <?php echo htmlspecialchars($review['author_name']); ?></h3>
                </div>
                <?php if ($params->get('show_rating', 1)): ?>
                    <div class="review-rating">
                        <?php echo str_repeat('★', $review['rating']); ?>
                        <span>(<?php echo $review['rating']; ?>/5)</span>
                    </div>
                <?php endif; ?>
                <div class="review-text">
                    <?php echo htmlspecialchars($review['text']); ?>
                </div>
                <?php if ($params->get('show_date', 1)): ?>
                    <div class="review-date">
                        <?php echo date($params->get('date_format', 'd/m/Y'), strtotime($review['time'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="no-reviews">Aucun avis trouvé.</p>
<?php endif; ?>
<p>hello<p>