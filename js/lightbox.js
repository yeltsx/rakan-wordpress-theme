document.addEventListener('DOMContentLoaded', function() {
    const galleries = document.querySelectorAll('.rakan-gallery');
    
    if (galleries.length === 0) return;
    
    // Cria lightbox
    const lightbox = document.createElement('div');
    lightbox.className = 'rakan-lightbox';
    lightbox.innerHTML = `
        <button class="rakan-lightbox-close" aria-label="Fechar">&times;</button>
        <button class="rakan-lightbox-nav rakan-lightbox-prev" aria-label="Anterior">&larr;</button>
        <button class="rakan-lightbox-nav rakan-lightbox-next" aria-label="Próximo">&rarr;</button>
        <div class="rakan-lightbox-counter"></div>
        <div class="rakan-lightbox-content">
            <img class="rakan-lightbox-image" src="" alt="">
            <div class="rakan-lightbox-caption"></div>
        </div>
    `;
    document.body.appendChild(lightbox);
    
    let currentGallery = [];
    let currentIndex = 0;
    
    const lightboxImg = lightbox.querySelector('.rakan-lightbox-image');
    const lightboxCaption = lightbox.querySelector('.rakan-lightbox-caption');
    const lightboxCounter = lightbox.querySelector('.rakan-lightbox-counter');
    const closeBtn = lightbox.querySelector('.rakan-lightbox-close');
    const prevBtn = lightbox.querySelector('.rakan-lightbox-prev');
    const nextBtn = lightbox.querySelector('.rakan-lightbox-next');
    
    // Adiciona eventos aos links da galeria
    galleries.forEach(gallery => {
        const links = gallery.querySelectorAll('.rakan-gallery-link');
        
        links.forEach((link, index) => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                currentGallery = Array.from(links).map(l => ({
                    src: l.href,
                    title: l.dataset.title || ''
                }));
                
                currentIndex = index;
                showImage();
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });
    });
    
    function showImage() {
        const item = currentGallery[currentIndex];
        lightboxImg.src = item.src;
        lightboxCaption.textContent = item.title;
        lightboxCounter.textContent = `${currentIndex + 1} / ${currentGallery.length}`;
        
        prevBtn.style.display = currentIndex > 0 ? 'flex' : 'none';
        nextBtn.style.display = currentIndex < currentGallery.length - 1 ? 'flex' : 'none';
    }
    
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    closeBtn.addEventListener('click', closeLightbox);
    
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            showImage();
        }
    });
    
    nextBtn.addEventListener('click', function() {
        if (currentIndex < currentGallery.length - 1) {
            currentIndex++;
            showImage();
        }
    });
    
    // Teclado
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft' && currentIndex > 0) {
            currentIndex--;
            showImage();
        }
        if (e.key === 'ArrowRight' && currentIndex < currentGallery.length - 1) {
            currentIndex++;
            showImage();
        }
    });
});