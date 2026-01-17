<?php get_header(); ?>

<main id="main" class="site-main" role="main">
    <div class="mx-auto" style="max-width: 960px;">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

                <header class="post-header mb-10">
                    <h1 class="post-title mb-4">
                        <?php the_title(); ?>
                    </h1>
                </header>

                <div class="post-content max-w-none">
                    <?php the_content(); ?>
                </div>

            </article>

        <?php endwhile; endif; ?>

    </div>
</main>

<?php get_footer(); ?>
