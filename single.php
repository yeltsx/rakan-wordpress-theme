<?php get_header(); ?>

<main id="main" class="site-main" role="main">
    <div class="mx-auto" style="max-width: 960px;">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php 
                        the_post_thumbnail('post-thumbnail', array(
                            'loading' => 'lazy',
                            'alt' => get_the_title()
                        )); 
                        ?>
                        <div class="post-overlay"></div>
                    </div>
                <?php endif; ?>

                <header class="post-header mb-10">
                    <h1 class="post-title pb-4">
                        <?php the_title(); ?>
                    </h1>

                    <div class="post-meta flex items-center gap-4 text-sm" style="color: var(--rakan-text-muted);">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <time datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </div>

                        <?php if (comments_open() || get_comments_number()) : ?>
                            <a href="#comments" class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <span><?php comments_number('0', '1', '%'); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="post-content prose max-w-none">
                    <?php the_content(); ?>
                </div>

            </article>

            <?php
            if (comments_open() || get_comments_number()) :
            ?>
                <section id="comments" class="comments-area mt-16">
                    <?php comments_template(); ?>
                </section>
            <?php endif; ?>

        <?php endwhile; endif; ?>

    </div>
</main>

<?php get_footer(); ?>
