<?php get_header(); ?>
<main class="main-content">
  <div class="container">

    <section class="section-search-blog">
      <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <label>
          <span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'textdomain'); ?></span>
          <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('Search …', 'placeholder', 'textdomain'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        </label>
        <button type="submit" class="search-submit">
          <span class="screen-reader-text"><?php echo _x('Search', 'submit button', 'textdomain'); ?></span>
          <?php echo esc_html_x('Search', 'submit button', 'textdomain'); ?>
        </button>
      </form>
    </section>

    <section class="section-featured-posts">
      <?php
      $featured_args = array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
      );

      $featured_posts = new WP_Query($featured_args);
      $post_counter   = 0;

      if ($featured_posts->have_posts()) :
        while ($featured_posts->have_posts()) : $featured_posts->the_post();
          $post_counter++;

          // Determine article class
          $post_class = ($post_counter === 1) ? 'featured-article-primary' : 'featured-article-secondary';
          if ($post_counter === 2) {
            $post_class .= ' first-secondary';
          }
      ?>
          <article class="featured-article <?php echo esc_attr($post_class); ?>">
            <!-- Featured image -->
            <figure class="post-card-img">
              <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
              </a>
            </figure>

            <!-- Article content -->
            <div class="featured-article-content">
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

              <div class="article-meta">
                <strong class="article-author">By <?php the_author(); ?></strong>
                <time class="article-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                  <?php echo get_the_date('F j, Y'); ?>
                </time>
              </div>

              <?php if ($post_counter === 1) : ?>
                <p><?php echo get_the_excerpt(); ?></p>
              <?php endif; ?>

            </div>
          </article>
      <?php
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </section>

    <section class="section-latest-posts">
      <div class="posts-grid">
        <?php
        $latest_posts = new WP_Query(array(
          'posts_per_page' => 16,
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

        <?php endwhile;
          wp_reset_postdata();
        endif; ?>
      </div>
    </section>
  </div>

</main>

<?php get_footer(); ?>