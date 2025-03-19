<?php if (!empty($reviews)): ?>
    <div class="google-reviews">
        <?php foreach ($reviews as $review): ?>
            <div class="review">
                <p><strong><?php echo htmlspecialchars($review['author_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($review['text']); ?></p>
                <p>Note: <?php echo $review['rating']; ?>/5</p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucun avis trouvé.</p>
<?php endif; ?>