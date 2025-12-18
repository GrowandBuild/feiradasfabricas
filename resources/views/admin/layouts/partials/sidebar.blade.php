<div class="col-md-3 col-lg-2 px-0">
    <div id="admin-sidebar" class="sidebar sidebar-collapsed">
        <div class="sidebar-header">
            @php
                $siteLogo = setting('site_logo') ?: 'logo-ofc.svg';
                if (\Illuminate\Support\Str::startsWith($siteLogo, ['http', 'https'])) {
                    $siteLogoUrl = $siteLogo;
                } else {
                    $siteLogoUrl = asset(\Illuminate\Support\Str::startsWith($siteLogo, 'storage/') ? $siteLogo : 'storage/' . $siteLogo);
                }
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" id="admin-logo-link">
                <img id="admin-site-logo" src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Feira das Fábricas') }}" 
                     style="height: 40px; width: auto; cursor: pointer;">
            </a>
        </div>

        <nav class="nav flex-column px-3">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> 
                <span>Dashboard</span>
            </a>

            <div class="nav-section">
                <div class="nav-section-title">Catálogo</div>

                <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    <span>Departamentos</span>
                </a>

                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Produtos</span>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-tags"></i>
                                <span>Categorias</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" href="{{ route('admin.attributes.index') }}">
                                <i class="bi bi-sliders"></i>
                                <span>Atributos</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                    <i class="bi bi-tag"></i> 
                    <span>Marcas</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                    <i class="bi bi-image"></i>
                    <span>Banners</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.albums.*') ? 'active' : '' }}" href="{{ route('admin.albums.index') }}">
                    <i class="bi bi-images"></i>
                    <span>Álbuns</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.department-badges.*') ? 'active' : '' }}" href="{{ route('admin.department-badges.index') }}">
                    <i class="bi bi-award"></i>
                    <span>Selos</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Operação</div>
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="bi bi-cart-check"></i> 
                    <span>Pedidos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                    <i class="bi bi-people"></i> 
                    <span>Clientes</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}" href="{{ route('admin.feedbacks.index') }}">
                    <i class="bi bi-chat-heart"></i>
                    <span>Feedbacks</span>
                </a>
                @if(setting('enable_physical_store_sync', false))
                    <a class="nav-link {{ request()->routeIs('admin.pdv.*') ? 'active' : '' }}" href="{{ route('admin.pdv.index') }}">
                        <i class="bi bi-cash-register"></i>
                        <span>PDV</span>
                    </a>
                @endif
                <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                    <i class="bi bi-ticket-perforated"></i> 
                    <span>Cupons</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.promotional-badges.*') ? 'active' : '' }}" href="{{ route('admin.promotional-badges.index') }}">
                    <i class="bi bi-tag-fill"></i>
                    <span>Badges Promocionais</span>
                </a>
            </div>

            <!-- Entregas Regionais - Link direto na lista principal -->
            <a class="nav-link {{ request()->routeIs('admin.regional-shipping.*') ? 'active' : '' }}" href="{{ route('admin.regional-shipping.index') }}">
                <i class="bi bi-truck"></i> 
                <span>Entregas Regionais</span>
            </a>

            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                <a class="nav-link {{ request()->routeIs('admin.instructions.*') ? 'active' : '' }}" href="{{ route('admin.instructions.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Guia Completo</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.homepage-sections.*') ? 'active' : '' }}" href="{{ route('admin.homepage-sections.index') }}">
                    <i class="bi bi-layout-three-columns"></i>
                    <span>Sessões</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> 
                    <span>Usuários</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <i class="bi bi-gear"></i> 
                    <span>Configurações</span>
                </a>
            </div>

            {{-- Links de frete removidos --}}
        </nav>

        <!-- Frases Motivacionais e Dicas de Negócio -->
        <div class="sidebar-quotes px-2 py-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="sidebar-quote sidebar-quote--purple small quote-expanded">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-images sidebar-quote-icon" style="color:#a855f7"></i>
                    <div class="quote-content">
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#a855f7">Imagem vende:</strong> Fotos de qualidade aumentam conversões em até 300%. Invista nisso!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--pink small quote-expanded">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-people-fill sidebar-quote-icon" style="color:#ec4899"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#ec4899">Cliente B2B é ouro:</strong> Eles compram volume. Ofereça condições especiais e fidelize!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--teal small">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-chat-dots-fill sidebar-quote-icon" style="color:#14b8a6"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#14b8a6">Descrições claras:</strong> Especificações técnicas detalhadas reduzem devoluções e aumentam confiança.
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--orange">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-tags-fill sidebar-quote-icon" style="color:#fb923c"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#fb923c">Categorias organizadas:</strong> Facilite a busca. Cliente que encontra rápido, compra mais!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--green">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-trophy-fill sidebar-quote-icon" style="color:#22c55e"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#22c55e">Fornecedores de confiança:</strong> Nomes fortes atraem e convertem!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--violet">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-phone-fill sidebar-quote-icon" style="color:#9333ea"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#9333ea">Smartphones lideram:</strong> Foco em celulares de qualidade. É o produto mais vendido no setor!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--yellow">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-cart-check-fill sidebar-quote-icon" style="color:#eab308"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#eab308">Acompanhe pedidos:</strong> Gestão eficiente = clientes satisfeitos = recompra garantida!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--cyan">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-cpu-fill sidebar-quote-icon" style="color:#06b6d4"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#06b6d4">Tecnologia atualizada:</strong> Lançamentos recentes geram buzz e atraem early adopters!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--amber">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-star-fill sidebar-quote-icon" style="color:#f59e0b"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#f59e0b">Destaque produtos:</strong> Use badges e banners para promover itens estratégicos!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--indigo">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-percent sidebar-quote-icon" style="color:#6366f1"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#6366f1">Cupons estratégicos:</strong> Descontos bem planejados impulsionam vendas em momentos certos!
                        </p>
                    </div>
                </div>
            </div>

            <div class="sidebar-quote sidebar-quote--lavender">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-rocket-takeoff-fill sidebar-quote-icon" style="color:#8b5cf6"></i>
                    <div>
                        <p class="mb-0 sidebar-quote-text">
                            <strong style="color:#8b5cf6">Sucesso é constância:</strong> Atualize diariamente, analise métricas e otimize sempre!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
