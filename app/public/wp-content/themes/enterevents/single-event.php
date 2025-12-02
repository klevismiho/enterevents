<?php get_header(); ?>

<section>
    <div class="hero-images">   
        <img src="<?php the_field('hero_image'); ?>" alt="">
    </div>
    <div class="event-the-content">
        <?php the_content(); ?>
    </div>
    <div class="info-div">
        <div>
            <h3>Date</h3>
            <h2><?php the_field('start_datetime'); ?></h2>  
        </div>
        <div>       
            <h3>Location</h3>
            <h2><?php the_field('location'); ?></h2>
        </div>
        <div>  
            <h3>Info</h3>
            <h2>Cel:+355 67 659 0000<br>Email: info@enterevents.al</h2>
        </div>
    </div>
</section>

<section class="section-event-article">
    <div class="container-normal">
        <article>
            <h1>Tickets</h1>
        </article>
    </div>
</section>

<section class="section-tickets">

    <?php
    $event_tickets = get_field('event_tickets');
    if( $event_tickets ): ?>
        <?php foreach( $event_tickets as $ticket ): 
        if($ticket->post_status == 'publish') {
            // Setup this post for WP functions (variable must be named $post).
            setup_postdata($post); ?>
            <div class="ticket-flex">
                <div class="ticket-bg">
                     <?php the_post_thumbnail(); ?>
                    <div class="ticket-title">
                        <div class="ticket-text">
                            <a href="<?php echo get_permalink( $ticket->ID ); ?>" class="image-link"><img src="<?php the_field('hero_image'); ?>" alt=""></a>
                            <div class="ticket-content">   
                                <h3><?php echo get_the_title( $ticket->ID ); ?></h3>
                                <a href="<?php echo get_permalink( $ticket->ID ); ?>" class="button">Buy Ticket</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php endforeach; ?>
        <?php 
        // Reset the global post object so that the rest of the page works correctly.
        wp_reset_postdata(); ?>
    <?php endif; ?>

    <style>
        .section-tickets .ticket-flex {
            padding: 0 !important;
        }
        .event-the-content {
            max-width: 560px;
            margin: 60px auto;
            border: 1px solid #000;
            padding: 30px;
        }
        .event-the-content p {
            margin-bottom: 15px;
        }
        .event-the-content ul {
            padding-left: 30px;
        }
        .event-the-content ul li {
            margin-bottom: 15px;
        }
    </style>

</section>