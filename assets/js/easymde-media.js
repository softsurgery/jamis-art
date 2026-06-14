(function (window) {
    function resolvePreviewUrl(path, previewBasePath) {
        if (/^https?:\/\//i.test(path) || path.startsWith('/')) {
            return path;
        }
        if (path.startsWith('storage/')) {
            return previewBasePath + path;
        }
        return path;
    }

    function buildImageMarkdown(path, alt, linkUrl) {
        const safeAlt = (alt || 'image').replace(/[\[\]()]/g, '');
        const imageMarkdown = `![${safeAlt}](${path})`;
        if (linkUrl && linkUrl.trim()) {
            return `[${imageMarkdown}](${linkUrl.trim()})`;
        }
        return imageMarkdown;
    }

    function createMediaPickerModal(options) {
        const apiUrl = options.apiUrl;
        const previewBasePath = options.previewBasePath || '../../../';
        let currentFolderId = 0;
        let selectedImage = null;

        const overlay = document.createElement('div');
        overlay.className = 'easymde-media-overlay';
        overlay.innerHTML = `
            <div class="easymde-media-modal" role="dialog" aria-modal="true" aria-labelledby="easymde-media-title">
                <div class="easymde-media-header">
                    <h2 id="easymde-media-title">Insert Image</h2>
                    <button type="button" class="easymde-media-close" aria-label="Close">&times;</button>
                </div>
                <div class="easymde-media-breadcrumbs"></div>
                <div class="easymde-media-grid"></div>
                <div class="easymde-media-form">
                    <label>
                        Alt text
                        <input type="text" class="easymde-media-alt" placeholder="Image description">
                    </label>
                    <label>
                        Link URL (optional)
                        <input type="url" class="easymde-media-link" placeholder="https://example.com">
                    </label>
                </div>
                <div class="easymde-media-footer">
                    <button type="button" class="easymde-media-cancel">Cancel</button>
                    <button type="button" class="easymde-media-insert" disabled>Insert</button>
                </div>
            </div>
        `;

        const breadcrumbsEl = overlay.querySelector('.easymde-media-breadcrumbs');
        const gridEl = overlay.querySelector('.easymde-media-grid');
        const altInput = overlay.querySelector('.easymde-media-alt');
        const linkInput = overlay.querySelector('.easymde-media-link');
        const insertBtn = overlay.querySelector('.easymde-media-insert');

        function close() {
            overlay.remove();
            document.body.style.overflow = '';
        }

        function setSelected(image) {
            selectedImage = image;
            insertBtn.disabled = !image;
            if (image) {
                altInput.value = image.slug.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ');
            }
        }

        async function loadFolder(folderId) {
            currentFolderId = folderId;
            selectedImage = null;
            insertBtn.disabled = true;
            gridEl.innerHTML = '<p class="easymde-media-loading">Loading...</p>';

            const response = await fetch(`${apiUrl}?folder=${folderId}`);
            if (!response.ok) {
                gridEl.innerHTML = '<p class="easymde-media-error">Failed to load files.</p>';
                return;
            }

            const data = await response.json();
            renderBreadcrumbs(data);
            renderGrid(data);
        }

        function renderBreadcrumbs(data) {
            breadcrumbsEl.innerHTML = data.breadcrumbs.map((crumb, index) => {
                const isLast = index === data.breadcrumbs.length - 1;
                if (isLast) {
                    return `<span class="easymde-media-crumb active">${crumb.name}</span>`;
                }
                return `<button type="button" class="easymde-media-crumb" data-folder-id="${crumb.id}">${crumb.name}</button>`;
            }).join('<span class="easymde-media-sep">/</span>');

            breadcrumbsEl.querySelectorAll('[data-folder-id]').forEach((button) => {
                button.addEventListener('click', () => loadFolder(parseInt(button.dataset.folderId, 10)));
            });
        }

        function renderGrid(data) {
            const items = [];

            if (data.parentFolderId !== data.folderId && data.folderId !== 0) {
                items.push(`
                    <button type="button" class="easymde-media-item easymde-media-back" data-folder-id="${data.parentFolderId}">
                        <span class="easymde-media-folder-icon">&#128193;</span>
                        <span>..</span>
                    </button>
                `);
            }

            data.folders.forEach((folder) => {
                items.push(`
                    <button type="button" class="easymde-media-item easymde-media-folder" data-folder-id="${folder.id}">
                        <span class="easymde-media-folder-icon">&#128193;</span>
                        <span>${folder.name}</span>
                    </button>
                `);
            });

            data.images.forEach((image) => {
                const previewUrl = resolvePreviewUrl(image.path, previewBasePath);
                items.push(`
                    <button type="button" class="easymde-media-item easymde-media-image" data-image-id="${image.id}">
                        <img src="${previewUrl}" alt="${image.slug}" loading="lazy">
                        <span>${image.slug}</span>
                    </button>
                `);
            });

            if (items.length === 0) {
                gridEl.innerHTML = '<p class="easymde-media-empty">No images in this folder.</p>';
                return;
            }

            gridEl.innerHTML = items.join('');

            gridEl.querySelectorAll('[data-folder-id]').forEach((button) => {
                button.addEventListener('click', () => loadFolder(parseInt(button.dataset.folderId, 10)));
            });

            gridEl.querySelectorAll('[data-image-id]').forEach((button) => {
                button.addEventListener('click', () => {
                    gridEl.querySelectorAll('.easymde-media-image.selected').forEach((el) => el.classList.remove('selected'));
                    button.classList.add('selected');
                    const image = data.images.find((item) => item.id === parseInt(button.dataset.imageId, 10));
                    setSelected(image);
                });
            });
        }

        overlay.querySelector('.easymde-media-close').addEventListener('click', close);
        overlay.querySelector('.easymde-media-cancel').addEventListener('click', close);
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                close();
            }
        });

        insertBtn.addEventListener('click', () => {
            if (!selectedImage || !options.onInsert) {
                return;
            }
            const markdown = buildImageMarkdown(
                selectedImage.path,
                altInput.value,
                linkInput.value
            );
            options.onInsert(markdown);
            close();
        });

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        loadFolder(0);

        return { close };
    }

    function enhancePreviewHtml(html, previewBasePath) {
        return html.replace(/(<img[^>]+src=")([^"]+)(")/gi, (_, prefix, src, suffix) => {
            return prefix + resolvePreviewUrl(src, previewBasePath) + suffix;
        });
    }

    window.initEasyMDEWithMedia = function (config) {
        const previewBasePath = config.previewBasePath || '../../../';
        const apiUrl = config.apiUrl;

        const easyMDE = new EasyMDE({
            element: config.element,
            spellChecker: false,
            previewRender: function (plainText) {
                const html = this.markdown(plainText);
                return enhancePreviewHtml(html, previewBasePath);
            },
            toolbar: [
                'bold',
                'italic',
                'heading',
                '|',
                'quote',
                'unordered-list',
                'ordered-list',
                '|',
                'link',
                {
                    name: 'insert-media',
                    action: function (editor) {
                        createMediaPickerModal({
                            apiUrl: apiUrl,
                            previewBasePath: previewBasePath,
                            onInsert: function (markdown) {
                                const doc = editor.codemirror.getDoc();
                                const cursor = doc.getCursor();
                                doc.replaceRange(markdown, cursor);
                            },
                        });
                    },
                    className: 'fa fa-image',
                    title: 'Insert image from files',
                },
                '|',
                'preview',
                'guide',
            ],
        });

        return easyMDE;
    };

    window.buildImageMarkdown = buildImageMarkdown;
})(window);
