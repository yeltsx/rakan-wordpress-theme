<?php get_header(); ?>

<main id="main" class="site-main" role="main">
    <div class="mx-auto px-4" style="max-width: 960px;">

        <?php if (have_posts()) : while (have_posts()) : the_post(); 
            $status = get_post_meta(get_the_ID(), '_meta_status', true) ?: 'em_andamento';
            $progresso = get_post_meta(get_the_ID(), '_meta_progresso', true);
            $categoria = get_post_meta(get_the_ID(), '_meta_categoria', true);
            $prioridade = get_post_meta(get_the_ID(), '_meta_prioridade', true);
            $data_inicio = get_post_meta(get_the_ID(), '_meta_data_inicio', true);
            $data_fim = get_post_meta(get_the_ID(), '_meta_data_fim', true);
        ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post single-meta-content'); ?>>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php 
                        the_post_thumbnail('post-thumbnail', array(
                            'loading' => 'eager',
                            'alt' => get_the_title()
                        )); 
                        ?>
                    </div>
                <?php endif; ?>

                <header class="post-header mb-10">
                    <div class="meta-header-single">
                        <span class="meta-badge">Meta</span>
                        <?php if ($categoria) : ?>
                            <span class="meta-categoria-badge-large"><?php echo get_meta_categoria_icon($categoria); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="post-title pb-4">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ($progresso) : ?>
                        <div class="meta-progress-wrapper-single">
                            <div class="meta-progress-info">
                                <span class="meta-progress-label">Progresso da Meta</span>
                                <span class="meta-progress-percent"><?php echo esc_html($progresso); ?>%</span>
                            </div>
                            <div class="meta-progress-bar-single">
                                <div class="meta-progress-fill" style="width: <?php echo esc_attr($progresso); ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="meta-details-grid">
                        <div class="meta-detail-card">
                            <span class="meta-detail-label">Status</span>
                            <span class="meta-status meta-status-<?php echo esc_attr($status); ?>">
                                <?php echo esc_html(get_meta_status_label($status)); ?>
                            </span>
                        </div>

                        <?php if ($data_inicio) : ?>
                            <div class="meta-detail-card">
                                <span class="meta-detail-label">Data de Início</span>
                                <span class="meta-detail-value">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <?php echo date_i18n('d/m/Y', strtotime($data_inicio)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($data_fim) : ?>
                            <div class="meta-detail-card">
                                <span class="meta-detail-label">Prazo Final</span>
                                <span class="meta-detail-value">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <?php echo date_i18n('d/m/Y', strtotime($data_fim)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($prioridade) : ?>
                            <div class="meta-detail-card">
                                <span class="meta-detail-label">Prioridade</span>
                                <span class="meta-prioridade meta-prioridade-<?php echo esc_attr($prioridade); ?>">
                                    <?php 
                                    $prioridades = array('baixa' => '🟢 Baixa', 'media' => '🟡 Média', 'alta' => '🔴 Alta');
                                    echo isset($prioridades[$prioridade]) ? $prioridades[$prioridade] : $prioridade;
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="post-content prose max-w-none">
                    <?php the_content(); ?>
                </div>

            </article>

            <?php if (comments_open() || get_comments_number()) : ?>
                <section id="comments" class="comments-area mt-16">
                    <?php comments_template(); ?>
                </section>
            <?php endif; ?>

        <?php endwhile; endif; ?>

    </div>
</main>

<?php get_footer(); ?>