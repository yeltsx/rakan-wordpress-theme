<?php get_header(); ?>

<main id="main" class="site-main" role="main">
    <div class="mx-auto" style="max-width: 960px;">
        
        <?php if (have_posts()) : ?>
            
            <?php 
            $post_count = 0;
            while (have_posts()) : the_post(); 
                $post_count++;
            ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php 
                            the_post_thumbnail('post-thumbnail', array(
                                'loading' => 'lazy',
                                'alt' => get_the_title()
                            )); 
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-content-wrapper">
                        <h2 class="post-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="post-meta flex justify-between items-center text-sm mb-6" style="color: var(--rakan-text-muted);">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>

                            <div class="flex items-center gap-4">
                                <button class="share-button flex items-center gap-1.5 transition-colors duration-200" data-url="<?php the_permalink(); ?>" data-title="<?php echo esc_attr(get_the_title()); ?>" aria-label="Compartilhar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="18" cy="5" r="3"/>
                                        <circle cx="6" cy="12" r="3"/>
                                        <circle cx="18" cy="19" r="3"/>
                                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                                    </svg>
                                </button>

                                <?php if (comments_open() || get_comments_number()) : ?>
                                    <a href="<?php comments_link(); ?>" class="flex items-center gap-1.5 transition-colors duration-200" style="color: var(--rakan-text-muted);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                        </svg>
                                        <span><?php comments_number('0', '1', '%'); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="post-excerpt">
                            <?php 
                            echo the_excerpt();
                            ?>
                            
                            <a href="<?php the_permalink(); ?>" class="read-more-link">
                                mais →
                            </a>
                        </div>
                    </div>
                </article>

                <?php 
                // Newsletter após o primeiro post (apenas na home)
                if ($post_count === 1 && is_home()) : 
                ?>
                    <div class="newsletter-box">
                        <h3 class="newsletter-title">Receba as atualizações</h3>
                        
                        <form class="newsletter-form" method="post" action="#">
                            <div class="flex gap-3 mb-4 flex-wrap">
                                <input 
                                    type="text" 
                                    name="newsletter_name" 
                                    placeholder="Seu nome" 
                                    required
                                    class="newsletter-input flex-1"
                                    style="min-width: 200px;"
                                />
                                <input 
                                    type="email" 
                                    name="newsletter_email" 
                                    placeholder="Seu e-mail" 
                                    required
                                    class="newsletter-input flex-1"
                                    style="min-width: 200px;"
                                />
                                <button type="submit" class="newsletter-button">
                                    Inscrever-se
                                </button>
                            </div>
                        </form>

                        <p class="newsletter-text text-sm" style="color: var(--rakan-text-secondary);">
                            Me siga no 
                            <a href="<?php echo esc_url(get_theme_mod('bluesky_url', '#')); ?>" target="_blank">Bluesky</a>, 
                            <a href="<?php echo esc_url(get_theme_mod('instagram_url', '#')); ?>" target="_blank">Instagram</a> 
                            ou entre na comunidade do 
                            <a href="<?php echo esc_url(get_theme_mod('discord_url', '#')); ?>" target="_blank">Discord</a>. 
                            Você também pode 
                            <a href="#" id="enable-notifications">se inscrever para receber notificações</a> 
                            e no 
                            <a href="<?php echo esc_url(get_feed_link()); ?>" target="_blank">Feed RSS</a>.
                        </p>
                    </div>
                <?php endif; ?>

            <?php endwhile; ?>

            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '← Anterior',
                    'next_text' => 'Próximo →',
                ));
                ?>
            </div>

        <?php else : ?>
            
            <div class="no-posts" style="text-align: center; padding: 3rem 0;">
                <p style="color: var(--rakan-text-secondary);">Nenhum post encontrado.</p>
            </div>

        <?php endif; ?>

    </div>
</main>

<script>
// Compartilhamento nativo com fallback
document.addEventListener('DOMContentLoaded', function() {
    const shareButtons = document.querySelectorAll('.share-button');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const url = this.dataset.url;
            const title = this.dataset.title;
            
            // Tenta usar API nativa de compartilhamento
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: title,
                        url: url
                    });
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        fallbackShare(url);
                    }
                }
            } else {
                fallbackShare(url);
            }
        });
    });
    
    // Fallback: copia URL para clipboard
    function fallbackShare(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copiado para a área de transferência!');
        }).catch(() => {
            prompt('Copie este link:', url);
        });
    }

    // Notificações push (se suportado)
    const notifButton = document.getElementById('enable-notifications');
    if (notifButton) {
        notifButton.addEventListener('click', function(e) {
            e.preventDefault();
            if ('Notification' in window) {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        alert('Notificações ativadas! Você receberá atualizações.');
                    }
                });
            } else {
                alert('Seu navegador não suporta notificações.');
            }
        });
    }
});
</script>

<?php get_footer(); ?>