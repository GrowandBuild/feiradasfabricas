<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $u)
  <url>
    <loc>{{ htmlspecialchars($u['loc'], ENT_XML1 | ENT_COMPAT, 'UTF-8') }}</loc>
    @if(!empty($u['lastmod']))<lastmod>{{ $u['lastmod'] }}</lastmod>@endif
    @if(!empty($u['changefreq']))<changefreq>{{ $u['changefreq'] }}</changefreq>@endif
    @if(!empty($u['priority']))<priority>{{ $u['priority'] }}</priority>@endif
  </url>
@endforeach
</urlset>
