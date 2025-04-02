<?php
\defined('_JEXEC') or die;


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