@php
    // Buscar embeds do cache ou API
    $embeds = cache()->remember('instagram_embed_urls_public', now()->addMinutes(30), function () {
        try {
            $response = Http::get(url('/api/instagram-embed/embeds'));
            if ($response->successful()) {
                $data = $response->json();
                return $data['embeds'] ?? [];
            }
        } catch (\Exception $e) {
            // Fallback para demonstração
            return [
                [
                    'url' => 'https://instagram.com/p/demo123',
                    'type' => 'post',
                    'thumbnail_url' => 'https://picsum.photos/300x400?random=1',
                    'title' => 'Novidades',
                    'caption' => 'Confira nossas novidades! 🎉',
                    'media_url' => 'https://picsum.photos/300x400?random=1',
                    'is_video' => false
                ],
                [
                    'url' => 'https://instagram.com/reel/demo456',
                    'type' => 'reel',
                    'thumbnail_url' => 'https://picsum.photos/300x400?random=2',
                    'title' => 'Promoções',
                    'caption' => 'Super promoções imperdíveis! 🔥',
                    'media_url' => 'https://picsum.photos/300x400?random=2',
                    'is_video' => true
                ]
            ];
        }
        return [];
    });
@endphp

@if(!empty($embeds))
<section class="instagram-embed-section">
    <div class="container">
        <div class="instagram-header text-center">
            <h2 class="instagram-title">
                <i class="bi bi-instagram me-2"></i>
                Destaques do Instagram
            </h2>
            <p class="instagram-subtitle">
                Acompanhe nossos posts e stories em tempo real
            </p>
        </div>

        <div class="instagram-embed-container">
            @foreach($embeds as $embed)
            <div class="instagram-embed-item" data-url="{{ $embed['url'] }}">
                <div class="embed-card">
                    <div class="embed-media">
                        @if($embed['is_video'])
                            <div class="video-placeholder">
                                <i class="bi bi-play-circle"></i>
                                <img src="{{ $embed['thumbnail_url'] }}" alt="{{ $embed['title'] }}" class="embed-thumbnail">
                            </div>
                        @else
                            <img src="{{ $embed['thumbnail_url'] }}" alt="{{ $embed['title'] }}" class="embed-thumbnail">
                        @endif
                    </div>
                    
                    <div class="embed-overlay">
                        <div class="embed-type-badge">
                            @switch($embed['type'])
                                @case('post')
                                    <span class="badge bg-primary">Post</span>
                                    @break
                                @case('reel')
                                    <span class="badge bg-danger">Reel</span>
                                    @break
                                @case('highlight')
                                    <span class="badge bg-warning text-dark">Highlight</span>
                                    @break
                                @case('tv')
                                    <span class="badge bg-info">IGTV</span>
                                    @break
                                @case('profile')
                                    <span class="badge bg-success">Perfil</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Instagram</span>
                            @endswitch
                        </div>
                        
                        <h3 class="embed-title">{{ $embed['title'] }}</h3>
                        
                        @if(!empty($embed['caption']))
                        <p class="embed-caption">{{ Str::limit($embed['caption'], 100) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.instagram-embed-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, #833ab4 0%, #fd1d1d 50%, #fcb045 100%);
    position: relative;
    overflow: hidden;
}

.instagram-embed-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="instagram-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23instagram-pattern)"/></svg>');
    opacity: 0.3;
}

.instagram-header {
    position: relative;
    z-index: 2;
    margin-bottom: 3rem;
}

.instagram-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.instagram-subtitle {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.95);
    max-width: 600px;
    margin: 0 auto;
}

.instagram-embed-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.instagram-embed-item {
    cursor: pointer;
    transition: transform 0.3s ease;
}

.instagram-embed-item:hover {
    transform: translateY(-5px);
}

.embed-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    aspect-ratio: 4/5;
    position: relative;
}

.embed-media {
    position: relative;
    width: 100%;
    height: 60%;
    overflow: hidden;
}

.embed-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.embed-card:hover .embed-thumbnail {
    transform: scale(1.05);
}

.video-placeholder {
    position: relative;
    width: 100%;
    height: 100%;
}

.video-placeholder i {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 3rem;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    z-index: 2;
}

.embed-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    padding: 1.5rem;
    color: white;
    text-align: center;
}

.embed-type-badge {
    margin-bottom: 0.5rem;
}

.embed-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.embed-caption {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .instagram-title {
        font-size: 2rem;
    }
    
    .instagram-embed-container {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .embed-card {
        aspect-ratio: 4/5;
    }
    
    .embed-title {
        font-size: 1rem;
    }
    
    .embed-caption {
        font-size: 0.8rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar clique nos cards para abrir o Instagram
    document.querySelectorAll('.instagram-embed-item').forEach(item => {
        item.addEventListener('click', function() {
            const url = this.dataset.url;
            window.open(url, '_blank');
        });
    });
});
</script>
@else
<section class="instagram-embed-section empty">
    <div class="container text-center">
        <i class="bi bi-instagram" style="font-size: 4rem; color: rgba(255,255,255,0.5);"></i>
        <h3 class="mt-3" style="color: white;">Nenhum embed encontrado</h3>
        <p style="color: rgba(255,255,255,0.8);">
            Configure os embeds do Instagram para exibir seus posts
        </p>
    </div>
</section>
@endif
