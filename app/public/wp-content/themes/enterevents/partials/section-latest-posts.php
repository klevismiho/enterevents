<section class="section-latest-posts">
  <div class="container">
    <header class="section-header">
      <h2>Latest Posts</h2>
      <p>From exclusive artist interviews to festival reviews and industry insights, our digital magazine keeps you in the know. Whether you’re a fan, artist, or industry insider, ENTER MAG provides news, stories, and features that connect you to the heart of the music scene.</p>
    </header>
    <div class="posts-grid">
      <?php
      $latest_posts = new WP_Query(array(
        'posts_per_page' => 6,
        'post_status'    => 'publish',
      ));

      if ($latest_posts->have_posts()) :
        while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>

          <article <?php post_class('post-card'); ?>>
            <?php if (has_post_thumbnail()) : ?>
              <div class="post-card-img">
                <a href="<?php the_permalink(); ?>">
                  <?php the_post_thumbnail('medium'); ?>
                </a>
              </div>
            <?php endif; ?>

            <div class="post-card-content">
              <!-- Meta div with date -->
              <div class="post-card-meta">
                <time datetime="<?php echo get_the_date('c'); ?>">
                  Posted on: <?php echo get_the_date(); ?>
                </time>
              </div>

              <h3 class="post-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h3>
              <p class="post-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
              <a href="<?php the_permalink(); ?>" class="post-card-readmore">Read More</a>
            </div>
          </article>

      <?php endwhile;
        wp_reset_postdata();
      endif; ?>
    </div>
  </div>
</section>