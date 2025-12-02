<?php get_header(); ?>

<div class="container">

  <section class="section-latest-posts">

    <header class="page-header">
      <?php if (have_posts()) : ?>
        <h1 class="page-title">
          <?php
          /* translators: %s: Search term. */
          printf(esc_html__('Search Results for: %s', 'textdomain'), '<span>' . get_search_query() . '</span>');
          ?>
        </h1>
      <?php else : ?>
        <h1 class="page-title"><?php esc_html_e('Nothing Found', 'textdomain'); ?></h1>
      <?php endif; ?>
    </header>

    <div class="posts-grid">
      <?php
      // Check if there are any posts returned by the main search query
      if (have_posts()) :
        // Start the Loop
        while (have_posts()) : the_post();
          // Use the post card structure from your index.php
      ?>

          <article <?php post_class('post-card'); ?>>
            <?php if (has_post_thumbnail()) : ?>
              <div class="post-card-img">
                <a href="<?php the_permalink(); ?>">
                  <?php the_post_thumbnail('medium'); ?>
                </a>
              </div>
            <?php endif; ?>

            <div class="post-card-content">
              <h3 class="post-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h3>

              <div class="article-meta">
                <strong class="article-author">By <?php the_author(); ?></strong>
                <time class="article-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                  <?php echo get_the_date('F j, Y'); ?>
                </time>
              </div>

              <p class="post-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
            </div>
          </article>

      <?php endwhile; // End the Loop 
      ?>

    </div> <?php
    // Optional: Add post navigation/pagination
    the_posts_navigation();

    else :
      // Content to display if no posts were found
    ?>
      <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'textdomain'); ?></p>
      <?php endif; ?>

  </section>

</div>

<?php get_footer(); ?>