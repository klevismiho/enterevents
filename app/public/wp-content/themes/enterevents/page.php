<?php get_header(); ?>

<main class="main-content">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <div class="container">
                <article>
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </article>
            </div>

        <?php endwhile;
    else : ?>
        <p><?php esc_html_e('Sorry, no posts matched your criteria.'); ?></p>
    <?php endif; ?>

</main>

<?php get_footer(); ?>