<?php if (!empty($reviews)): ?>
    <div class="google-reviews">
        <?php foreach ($reviews as $review): ?>
            <div class="review">
                <p><strong><?php echo htmlspecialchars($review['author_name']); ?></strong></p>
                <?php if ($params->get('show_rating', 1)): ?>
                    <p>Note: <?php echo $review['rating']; ?>/5</p>
                <?php endif; ?>
                <p><?php echo htmlspecialchars($review['text']); ?></p>
                <?php if ($params->get('show_date', 1)): ?>
                    <p>Date: <?php echo date($params->get('date_format', 'd/m/Y'), strtotime($review['time'])); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucun avis trouvé.</p>
<?php endif; ?>