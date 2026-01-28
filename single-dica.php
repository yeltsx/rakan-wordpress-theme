<?php get_header(); ?>

<main id="main" class="site-main" role="main">
    <div class="mx-auto px-4" style="max-width: 800px;">
        
        <?php while (have_posts()) : the_post(); 
            $tipo = get_post_meta(get_the_ID(), '_dica_tipo', true) ?: 'geral';
            $language = get_post_meta(get_the_ID(), '_dica_language', true);
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('dica-single'); ?>>
            
            <header class="dica-single-header">
                <div class="dica-single-meta">
                    <span class="dica-badge">
                        <?php echo get_dica_tipo_icon($tipo); ?>
                        <?php echo get_dica_tipo_label($tipo); ?>
                    </span>
                    <?php if ($language) : ?>
                        <span class="dica-language-badge"><?php echo esc_html($language); ?></span>
                    <?php endif; ?>
                </div>

                <h1 class="dica-single-title"><?php the_title(); ?></h1>

                <div class="dica-single-info">
                    <time datetime="<?php echo get_the_date('c'); ?>">
                        <?php echo get_the_date('d \d\e F \d\e Y'); ?>
                    </time>
                    <span class="separator">•</span>
                    <span><?php echo str_word_count(wp_strip_all_tags(get_the_content())); ?> palavras</span>
                    <?php if (comments_open() || get_comments_number()) : ?>
                        <span class="separator">•</span>
                        <a href="#comments">
                            <?php comments_number('0 comentários', '1 comentário', '% comentários'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <div class="dica-single-content">
                <?php the_content(); ?>
            </div>

            <?php if (has_tag()) : ?>
            <footer class="dica-single-footer">
                <div class="dica-tags">
                    <?php the_tags('<span class="tags-label">Tags:</span> ', ', ', ''); ?>
                </div>
            </footer>
            <?php endif; ?>

        </article>

        <?php
        // Navegação anterior/próximo
        $prev_post = get_previous_post();
        $next_post = get_next_post();
        
        if ($prev_post || $next_post) :
        ?>
        <nav class="dica-navigation">
            <?php if ($prev_post) : ?>
                <a href="<?php echo get_permalink($prev_post); ?>" class="nav-previous">
                    <span class="nav-label">← Dica Anterior</span>
                    <span class="nav-title"><?php echo get_the_title($prev_post); ?></span>
                </a>
            <?php endif; ?>

            <?php if ($next_post) : ?>
                <a href="<?php echo get_permalink($next_post); ?>" class="nav-next">
                    <span class="nav-label">Próxima Dica →</span>
                    <span class="nav-title"><?php echo get_the_title($next_post); ?></span>
                </a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php
        // Comentários
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

        <?php endwhile; ?>

    </div>
</main>

<style>
.dica-single {
    background: var(--rakan-surface-1);
    border-radius: var(--rakan-radius-lg);
    padding: 2.5rem;
    margin: 2rem 0;
}

.dica-single-header {
    border-bottom: 2px solid var(--rakan-border);
    padding-bottom: 2rem;
    margin-bottom: 2rem;
}

.dica-single-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.dica-single-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--rakan-text-primary);
    line-height: 1.2;
    margin-bottom: 1rem;
    font-family: var(--rakan-font-heading);
}

.dica-single-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    font-size: 0.9rem;
    color: var(--rakan-text-muted);
}

.dica-single-info .separator {
    color: var(--rakan-border);
}

.dica-single-info a {
    color: var(--rakan-text-muted);
    text-decoration: none;
    transition: color 0.3s;
}

.dica-single-info a:hover {
    color: var(--rakan-primary);
}

.dica-single-content {
    font-size: 1.0625rem;
    line-height: 1.8;
    color: var(--rakan-text-secondary);
}

.dica-single-content h2,
.dica-single-content h3,
.dica-single-content h4 {
    color: var(--rakan-text-primary);
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.dica-single-content p {
    margin-bottom: 1.5rem;
}

.dica-single-content pre {
    background: var(--rakan-surface-2);
    border: 1px solid var(--rakan-border);
    border-radius: var(--rakan-radius-md);
    padding: 1.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.dica-single-content code {
    background: var(--rakan-surface-2);
    padding: 0.2rem 0.5rem;
    border-radius: var(--rakan-radius-sm);
    font-size: 0.9em;
    font-family: var(--rakan-font-mono);
}

.dica-single-content pre code {
    background: none;
    padding: 0;
}

.dica-single-footer {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--rakan-border);
}

.dica-tags {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.tags-label {
    font-weight: 600;
    color: var(--rakan-text-primary);
}

.dica-tags a {
    padding: 0.5rem 1rem;
    background: var(--rakan-surface-2);
    color: var(--rakan-text-secondary);
    text-decoration: none;
    border-radius: var(--rakan-radius-sm);
    font-size: 0.875rem;
    transition: all 0.3s;
}

.dica-tags a:hover {
    background: var(--rakan-primary-soft);
    color: var(--rakan-primary);
}

.dica-navigation {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin: 2rem 0;
}

.nav-previous,
.nav-next {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1.5rem;
    background: var(--rakan-surface-1);
    border: 1px solid var(--rakan-border);
    border-radius: var(--rakan-radius-md);
    text-decoration: none;
    transition: all 0.3s;
}

.nav-previous:hover,
.nav-next:hover {
    border-color: var(--rakan-primary);
    transform: translateY(-2px);
}

.nav-next {
    text-align: right;
}

.nav-label {
    font-size: 0.875rem;
    color: var(--rakan-text-muted);
    font-weight: 600;
}

.nav-title {
    color: var(--rakan-text-primary);
    font-weight: 600;
}

@media (max-width: 768px) {
    .dica-single {
        padding: 1.5rem;
    }
    
    .dica-single-title {
        font-size: 2rem;
    }
    
    .dica-navigation {
        grid-template-columns: 1fr;
    }
    
    .nav-next {
        text-align: left;
    }
}
</style>

<?php get_footer(); ?>