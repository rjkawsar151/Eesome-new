@extends('layouts.admin')
@section('title', 'Media Library')
@section('heading', 'Media Library')

@push('styles')
<style>
    .media-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .media-stats-row {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .75rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
        text-decoration: none;
        background: #fff;
        border: 1px solid var(--line);
        color: var(--ink);
        transition: all .15s ease;
    }
    .stat-pill:hover, .stat-pill.active {
        background: var(--soft);
        border-color: var(--brand);
        color: var(--brand);
    }
    .stat-pill .count {
        background: rgba(0,0,0,.06);
        padding: .1rem .45rem;
        border-radius: 999px;
        font-size: .75rem;
    }
    .stat-pill.active .count {
        background: var(--brand);
        color: #fff;
    }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 1.25rem;
    }
    .media-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: .85rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        position: relative;
    }
    .media-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(15,23,42,.08);
        border-color: #cbd5e1;
    }
    .media-thumb-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        background-color: #f8fafc;
        background-image: 
            linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
            linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
            linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
            linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid var(--line);
    }
    .media-thumb {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: .5rem;
        transition: transform .2s ease;
    }
    .media-card:hover .media-thumb {
        transform: scale(1.04);
    }
    .media-badge {
        position: absolute;
        top: .6rem;
        right: .6rem;
        padding: .25rem .55rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
        box-shadow: 0 2px 6px rgba(0,0,0,.12);
        z-index: 2;
    }
    .badge-in-use {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-unused {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .media-body {
        padding: .85rem;
        display: flex;
        flex-direction: column;
        gap: .4rem;
        flex: 1;
    }
    .media-title {
        font-weight: 700;
        font-size: .88rem;
        line-height: 1.3;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--ink);
    }
    .media-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .75rem;
        color: var(--muted);
    }
    .media-path-text {
        font-size: .72rem;
        color: #94a3b8;
        font-family: ui-monospace, monospace;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .media-actions {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: .45rem;
        margin-top: auto;
        padding-top: .65rem;
        border-top: 1px solid #f1f5f9;
    }
    .btn-copy {
        width: 100%;
        gap: .35rem;
        font-size: .8rem;
        padding: .4rem .6rem;
    }
    .btn-copy.copied {
        background: #10b981 !important;
        color: #fff !important;
    }
    .btn-trash {
        padding: .4rem .65rem;
        font-size: .8rem;
    }

    /* Modals */
    .media-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s ease;
    }
    .media-modal-backdrop.open {
        opacity: 1;
        pointer-events: auto;
    }
    .media-modal {
        background: #fff;
        border-radius: 1rem;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        overflow: hidden;
        transform: scale(0.95);
        transition: transform .2s ease;
    }
    .media-modal-backdrop.open .media-modal {
        transform: scale(1);
    }
    .modal-head {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        border-bottom: 1px solid var(--line);
    }
    .modal-head.warning {
        background: #fffbeb;
        border-bottom-color: #fde68a;
    }
    .modal-head.danger {
        background: #fef2f2;
        border-bottom-color: #fecaca;
    }
    .modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .modal-head.warning .modal-icon {
        background: #fef3c7;
        color: #d97706;
    }
    .modal-head.danger .modal-icon {
        background: #fee2e2;
        color: #dc2626;
    }
    .modal-head h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1.25;
    }
    .modal-head.warning h3 {
        color: #92400e;
    }
    .modal-head.danger h3 {
        color: #991b1b;
    }
    .modal-body {
        padding: 1.25rem 1.5rem;
        max-height: 60vh;
        overflow-y: auto;
        font-size: .92rem;
        line-height: 1.5;
    }
    .modal-foot {
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid var(--line);
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
    }
    .usage-list {
        list-style: none;
        padding: 0;
        margin: .75rem 0 0;
        display: grid;
        gap: .5rem;
    }
    .usage-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .6rem;
        padding: .65rem .85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }
    .usage-item-info {
        display: grid;
        gap: .15rem;
        min-width: 0;
    }
    .usage-item-type {
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #be185d;
    }
    .usage-item-label {
        font-size: .88rem;
        font-weight: 700;
        color: var(--ink);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .usage-item-link {
        font-size: .8rem;
        font-weight: 700;
        color: var(--brand);
        text-decoration: none;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }
    .usage-item-link:hover {
        text-decoration: underline;
    }

    /* Toast Notification */
    #media-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #1e293b;
        color: #fff;
        padding: .75rem 1.25rem;
        border-radius: .65rem;
        font-weight: 700;
        font-size: .9rem;
        box-shadow: 0 10px 25px rgba(0,0,0,.2);
        z-index: 150;
        display: flex;
        align-items: center;
        gap: .6rem;
        transform: translateY(100px);
        opacity: 0;
        transition: transform .25s ease, opacity .25s ease;
        pointer-events: none;
    }
    #media-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="media-header">
    <div>
        <h1 class="title">Media Library</h1>
        <p class="subtle">All images across your store (products, variants, categories, blog posts, reviews, and uploads).</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="toggleUploadCard()">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:.4rem"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
        Upload New Image
    </button>
</div>

<!-- Upload form card (collapsible) -->
<div id="upload-card" class="card" style="display:none;margin-bottom:1.25rem;background:#fdf2f8;border-color:#fbcfe8">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
        <strong style="color:#831843;font-size:1rem">Upload image to media library</strong>
        <button type="button" class="btn btn-soft btn-sm" onclick="toggleUploadCard()">Close</button>
    </div>
    <form class="form-grid" method="POST" enctype="multipart/form-data" action="{{ route('admin.media.store') }}">
        @csrf
        <div class="field">
            <label for="media_file">Image File <span class="req">*</span></label>
            <input id="media_file" class="input" type="file" name="file" accept="image/png,image/webp,image/jpeg,image/gif,image/svg+xml,image/avif" required>
            <small class="subtle">Supported formats: WEBP, PNG, JPG, GIF, SVG, AVIF (Max: 5MB)</small>
        </div>
        <div class="field">
            <label for="alt_text">Alternative Text (Alt Text)</label>
            <input id="alt_text" class="input" name="alt_text" placeholder="Short description for accessibility">
        </div>
        <div class="full" style="text-align:right">
            <button type="submit" class="btn btn-primary">Upload to Library</button>
        </div>
    </form>
</div>

<!-- Search & Filters Toolbar -->
<div class="card" style="margin-bottom:1.25rem;padding:.85rem 1.1rem">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
        <!-- Stat Pills / Filters -->
        <div class="media-stats-row">
            <a href="{{ route('admin.media.index', array_merge(request()->except('page', 'usage'), ['usage' => 'all'])) }}" class="stat-pill {{ $usageFilter === 'all' ? 'active' : '' }}">
                <span>All Images</span>
                <span class="count">{{ $totalCount }}</span>
            </a>
            <a href="{{ route('admin.media.index', array_merge(request()->except('page', 'usage'), ['usage' => 'in_use'])) }}" class="stat-pill {{ $usageFilter === 'in_use' ? 'active' : '' }}">
                <span style="color:#b45309">● In Use</span>
                <span class="count">{{ $inUseCount }}</span>
            </a>
            <a href="{{ route('admin.media.index', array_merge(request()->except('page', 'usage'), ['usage' => 'unused'])) }}" class="stat-pill {{ $usageFilter === 'unused' ? 'active' : '' }}">
                <span style="color:#15803d">● Unused</span>
                <span class="count">{{ $unusedCount }}</span>
            </a>
        </div>

        <!-- Search input -->
        <form method="GET" action="{{ route('admin.media.index') }}" style="display:flex;gap:.5rem;align-items:center;flex:1 1 240px;max-width:400px">
            @if(request('usage'))<input type="hidden" name="usage" value="{{ request('usage') }}">@endif
            <input class="input" style="padding:.45rem .75rem;font-size:.88rem" name="q" value="{{ request('q') }}" placeholder="Search image name or path...">
            <button type="submit" class="btn btn-soft btn-sm">Search</button>
            @if(request('q'))
                <a href="{{ route('admin.media.index', request()->except('q')) }}" class="btn btn-soft btn-sm" title="Clear search">Clear</a>
            @endif
        </form>
    </div>
</div>

<!-- Images Grid -->
@if($assets->isEmpty())
    <div class="card" style="text-align:center;padding:3.5rem 1.5rem">
        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem;color:var(--muted)"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        <h3 style="margin:0 0 .5rem;font-size:1.2rem">No images found</h3>
        <p class="subtle" style="margin:0 0 1.25rem">
            @if(request('q') || request('usage'))
                No images matched your filter criteria. Try clearing your filters or search terms.
            @else
                Your media library currently has no images stored.
            @endif
        </p>
        @if(request('q') || request('usage'))
            <a href="{{ route('admin.media.index') }}" class="btn btn-soft">Reset Filters</a>
        @endif
    </div>
@else
    <div class="media-grid">
        @foreach($assets as $asset)
            <article class="media-card" id="media-card-{{ $asset->id }}">
                <div class="media-thumb-wrap">
                    @if($asset->is_in_use)
                        <span class="media-badge badge-in-use" title="Used in {{ count($asset->usage) }} location(s)">
                            ● In Use ({{ count($asset->usage) }})
                        </span>
                    @else
                        <span class="media-badge badge-unused" title="Not linked to any products or content">
                            ● Unused
                        </span>
                    @endif

                    <img class="media-thumb" src="{{ $asset->url }}" alt="{{ $asset->alt_text ?: $asset->original_name }}" loading="lazy" onerror="this.src='{{ asset('images/handbag-placeholder.svg') }}'">
                </div>

                <div class="media-body">
                    <h4 class="media-title" title="{{ $asset->original_name }}">{{ $asset->original_name }}</h4>
                    
                    <div class="media-meta">
                        <span>{{ $asset->formatted_size }}</span>
                        <span>{{ strtoupper(pathinfo($asset->path, PATHINFO_EXTENSION)) }}</span>
                    </div>

                    <div class="media-path-text" title="{{ $asset->path }}">
                        /{{ $asset->path }}
                    </div>

                    <div class="media-actions">
                        <button type="button" class="btn btn-soft btn-copy" onclick="copyImageLink('{{ $asset->url }}', this)">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            <span>Copy Link</span>
                        </button>

                        <button type="button" class="btn btn-danger btn-trash" title="Delete Image" onclick="handleDeleteClick({{ $asset->id }}, '{{ addslashes($asset->original_name) }}', {{ $asset->is_in_use ? 'true' : 'false' }}, {{ json_encode($asset->usage) }}, '{{ route('admin.media.destroy', $asset) }}')">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="pagination">
        {{ $assets->links() }}
    </div>
@endif

<!-- In Use Alert Modal -->
<div id="inuse-modal-backdrop" class="media-modal-backdrop" onclick="if(event.target === this) closeInUseModal()">
    <div class="media-modal" role="dialog" aria-modal="true" aria-labelledby="inuse-title">
        <div class="modal-head warning">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            </div>
            <div>
                <h3 id="inuse-title">Image In Use — Cannot Delete</h3>
                <span class="subtle" style="font-size:.82rem">This image is currently active in your store.</span>
            </div>
        </div>
        <div class="modal-body">
            <p style="margin:0 0 .75rem">
                The image <strong id="inuse-filename" style="color:var(--ink)"></strong> is currently linked to <strong id="inuse-count-text"></strong> across your catalog and content:
            </p>

            <ul id="inuse-items-list" class="usage-list">
                <!-- Dynamically populated -->
            </ul>

            <div style="margin-top:1.1rem;padding:.75rem .9rem;background:#fffbeb;border:1px solid #fde68a;border-radius:.6rem;font-size:.84rem;color:#92400e;line-height:1.45">
                💡 <strong>How to remove:</strong> To delete this image, please edit the item(s) listed above and replace or remove this image first.
            </div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-soft" onclick="closeInUseModal()">I Understand / Close</button>
        </div>
    </div>
</div>

<!-- Confirm Deletion Modal (for unused files) -->
<div id="delete-modal-backdrop" class="media-modal-backdrop" onclick="if(event.target === this) closeDeleteModal()">
    <div class="media-modal" role="dialog" aria-modal="true" aria-labelledby="delete-title">
        <div class="modal-head danger">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
            </div>
            <div>
                <h3 id="delete-title">Delete Media File?</h3>
                <span class="subtle" style="font-size:.82rem">Permanent deletion from storage.</span>
            </div>
        </div>
        <div class="modal-body">
            <p style="margin:0 0 .5rem">
                Are you sure you want to permanently delete <strong id="delete-filename" style="color:var(--ink)"></strong>?
            </p>
            <p class="subtle" style="margin:0;font-size:.85rem">
                This image is not linked to any products or content. This action cannot be undone.
            </p>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-soft" onclick="closeDeleteModal()">Cancel</button>
            <form id="delete-form" method="POST" action="" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Yes, Delete Image</button>
            </form>
        </div>
    </div>
</div>

<!-- Toast notification element -->
<div id="media-toast" role="alert" aria-live="polite">
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toast-message">Image link copied to clipboard!</span>
</div>
@endsection

@push('scripts')
<script>
    function toggleUploadCard() {
        const card = document.getElementById('upload-card');
        card.style.display = card.style.display === 'none' ? 'block' : 'none';
        if (card.style.display === 'block') {
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function showToast(msg) {
        const toast = document.getElementById('media-toast');
        const msgSpan = document.getElementById('toast-message');
        if (!toast) return;
        msgSpan.textContent = msg || 'Image link copied to clipboard!';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2600);
    }

    function copyImageLink(url, btn) {
        if (!navigator.clipboard) {
            // Fallback
            const el = document.createElement('textarea');
            el.value = url;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        } else {
            navigator.clipboard.writeText(url);
        }

        if (btn) {
            const span = btn.querySelector('span');
            const originalText = span ? span.textContent : 'Copy Link';
            btn.classList.add('copied');
            if (span) span.textContent = 'Copied!';

            setTimeout(() => {
                btn.classList.remove('copied');
                if (span) span.textContent = originalText;
            }, 2000);
        }

        showToast('Image link copied to clipboard!');
    }

    function handleDeleteClick(id, filename, isInUse, usageList, deleteUrl) {
        if (isInUse && usageList && usageList.length > 0) {
            openInUseModal(filename, usageList);
        } else {
            openDeleteModal(filename, deleteUrl);
        }
    }

    function openInUseModal(filename, usageList) {
        document.getElementById('inuse-filename').textContent = filename;
        document.getElementById('inuse-count-text').textContent = usageList.length + (usageList.length === 1 ? ' item' : ' items');
        
        const listEl = document.getElementById('inuse-items-list');
        listEl.innerHTML = '';

        usageList.forEach(item => {
            const li = document.createElement('li');
            li.className = 'usage-item';

            const info = document.createElement('div');
            info.className = 'usage-item-info';

            const typeSpan = document.createElement('span');
            typeSpan.className = 'usage-item-type';
            typeSpan.textContent = item.type || 'Item';

            const labelSpan = document.createElement('span');
            labelSpan.className = 'usage-item-label';
            labelSpan.textContent = item.label || 'Attached item';

            info.appendChild(typeSpan);
            info.appendChild(labelSpan);
            li.appendChild(info);

            if (item.url) {
                const link = document.createElement('a');
                link.className = 'usage-item-link';
                link.href = item.url;
                link.target = '_blank';
                link.rel = 'noopener';
                link.innerHTML = 'View / Edit &rarr;';
                li.appendChild(link);
            }

            listEl.appendChild(li);
        });

        document.getElementById('inuse-modal-backdrop').classList.add('open');
    }

    function closeInUseModal() {
        document.getElementById('inuse-modal-backdrop').classList.remove('open');
    }

    function openDeleteModal(filename, deleteUrl) {
        document.getElementById('delete-filename').textContent = filename;
        document.getElementById('delete-form').action = deleteUrl;
        document.getElementById('delete-modal-backdrop').classList.add('open');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal-backdrop').classList.remove('open');
    }

    // Escape key listener for modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeInUseModal();
            closeDeleteModal();
        }
    });
</script>
@endpush
