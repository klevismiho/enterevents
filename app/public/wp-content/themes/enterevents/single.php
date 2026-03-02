<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <main class="main-content">
            <div class="container">
                <article class="single-post-article">
                    <figure class="single-post-article-featured-image">
                        <?php the_post_thumbnail(); ?>
                    </figure>
                    <div class="single-post-article-content">
                        <div class="article-content">
                            <h1><?php the_title(); ?></h1>
                            <div class="post-meta">
                                Posted by <?php the_author(); ?> on
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>
                            <?php the_content(); ?>
                        </div>
                        <aside class="sidebar">
                            <div class="widget-author">
                                <div class="author-header">

                                    <?php
                                    $author_id = get_the_author_meta('ID');
                                    $image = get_field('profile_picture', 'user_' . $author_id);
                                    if ($image) {
                                       echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr(get_the_author()) . '" width="96" height="96" />';
                                    } else {
                                        echo get_avatar($author_id, 96);
                                    }
                                    ?>

                                    <div class="author-meta">
                                        <h5><?php the_author(); ?></h5>
                                        <h6><?php the_field('title', 'user_' . $author_id); ?></h6>
                                    </div>
                                </div>

                                <p><?php the_author_meta('description'); ?></p>
                            </div>
                            <!-- <div class="widget-ads">
     
                            </div> -->
                        </aside>

                    </div>
                </article>
            </div>
        </main>

        <section class="section-latest-posts">
            <div class="container">
                <header class="section-header">
                    <h2>More from the Magazine</h2>
                </header>
                <div class="posts-grid">
                    <?php
                    $latest_posts = new WP_Query(array(
                        'posts_per_page' => 3,
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
                                            <?php echo get_the_date(); ?>
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

<?php endwhile;
endif; ?>

<?php get_footer(); ?>