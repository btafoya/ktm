window.TiptapEditor = (function() {
    let editor = null;
    let editorElement = null;

    function initEditor(selector, options = {}) {
        const defaultOptions = {
            content: options.content || '',
            editable: true,
            extensions: [
                StarterKit,
                TaskList,
                TaskItem.configure({
                    nested: true,
                }),
            ],
            editorProps: {
                attributes: {
                    class: 'tiptap-editor-content prose prose-invert max-w-none bg-dark-subtle text-light p-3 rounded border border-secondary min-h-[150px]',
                },
            },
            onUpdate: options.onUpdate || (() => {}),
        };

        editorElement = typeof selector === 'string'
            ? document.querySelector(selector)
            : selector;

        if (!editorElement) {
            console.error('TipTap editor element not found:', selector);
            return null;
        }

        editor = new Editor(defaultOptions);

        return editor;
    }

    function getEditor() {
        return editor;
    }

    function getContent() {
        if (!editor) return '';

        const html = editor.getHTML();

        if (typeof TurndownService !== 'undefined') {
            const turndown = new TurndownService({
                headingStyle: 'atx',
                codeBlockStyle: 'fenced',
                emDelimiter: '*',
                strongDelimiter: '**',
                linkStyle: 'inlined',
                linkReferenceStyle: 'full',
            });

            turndown.addRule('taskList', {
                filter: function(node) {
                    return node.nodeName === 'UL' && node.getAttribute('data-type') === 'taskList';
                },
                replacement: function(content, node) {
                    return content;
                }
            });

            turndown.addRule('taskItem', {
                filter: function(node) {
                    return node.nodeName === 'LI' && node.getAttribute('data-type') === 'taskItem';
                },
                replacement: function(content, node) {
                    const checked = node.getAttribute('data-checked') === 'true';
                    const checkbox = checked ? '[x] ' : '[ ] ';
                    return checkbox + content;
                }
            });

            return turndown.turndown(html);
        }

        return html;
    }

    function setContent(markdown) {
        if (!editor) return;

        let html = markdown;

        if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
            html = marked.parse(markdown);
        }

        editor.commands.setContent(html);
    }

    function getHTML() {
        return editor ? editor.getHTML() : '';
    }

    function destroy() {
        if (editor) {
            editor.destroy();
            editor = null;
            editorElement = null;
        }
    }

    function createToolbar(targetElement) {
        const toolbar = document.createElement('div');
        toolbar.className = 'tiptap-toolbar d-flex gap-2 mb-2 flex-wrap';

        const buttons = [
            {
                icon: 'type-bold',
                title: 'Bold',
                action: () => editor.chain().focus().toggleBold().run(),
                isActive: () => editor.isActive('bold'),
            },
            {
                icon: 'type-italic',
                title: 'Italic',
                action: () => editor.chain().focus().toggleItalic().run(),
                isActive: () => editor.isActive('italic'),
            },
            {
                icon: 'type-underline',
                title: 'Strike',
                action: () => editor.chain().focus().toggleStrike().run(),
                isActive: () => editor.isActive('strike'),
            },
            { divider: true },
            {
                icon: 'list-ul',
                title: 'Bullet List',
                action: () => editor.chain().focus().toggleBulletList().run(),
                isActive: () => editor.isActive('bulletList'),
            },
            {
                icon: 'list-ol',
                title: 'Ordered List',
                action: () => editor.chain().focus().toggleOrderedList().run(),
                isActive: () => editor.isActive('orderedList'),
            },
            {
                icon: 'check2-square',
                title: 'Task List',
                action: () => editor.chain().focus().toggleTaskList().run(),
                isActive: () => editor.isActive('taskList'),
            },
            { divider: true },
            {
                icon: 'type-h1',
                title: 'Heading 1',
                action: () => editor.chain().focus().toggleHeading({ level: 1 }).run(),
                isActive: () => editor.isActive('heading', { level: 1 }),
            },
            {
                icon: 'type-h2',
                title: 'Heading 2',
                action: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
                isActive: () => editor.isActive('heading', { level: 2 }),
            },
            {
                icon: 'type-h3',
                title: 'Heading 3',
                action: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
                isActive: () => editor.isActive('heading', { level: 3 }),
            },
            { divider: true },
            {
                icon: 'blockquote',
                title: 'Blockquote',
                action: () => editor.chain().focus().toggleBlockquote().run(),
                isActive: () => editor.isActive('blockquote'),
            },
            {
                icon: 'code',
                title: 'Code Block',
                action: () => editor.chain().focus().toggleCodeBlock().run(),
                isActive: () => editor.isActive('codeBlock'),
            },
            { divider: true },
            {
                icon: 'link-45deg',
                title: 'Link',
                action: () => {
                    const url = prompt('Enter URL:');
                    if (url) {
                        editor.chain().focus().setLink({ href: url }).run();
                    }
                },
                isActive: () => editor.isActive('link'),
            },
            {
                icon: 'x-lg',
                title: 'Remove Format',
                action: () => editor.chain().focus().unsetAllMarks().run(),
                isActive: () => false,
            },
        ];

        buttons.forEach(btn => {
            if (btn.divider) {
                const divider = document.createElement('div');
                divider.className = 'vr';
                toolbar.appendChild(divider);
            } else {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-sm btn-outline-secondary';
                button.title = btn.title;
                button.innerHTML = `<i class="bi bi-${btn.icon}"></i>`;

                if (btn.isActive) {
                    button.addEventListener('click', () => {
                        btn.action();
                        updateActiveButtons(toolbar);
                    });
                } else {
                    button.addEventListener('click', btn.action);
                }

                button.dataset.action = btn.icon;
                toolbar.appendChild(button);
            }
        });

        function updateActiveButtons(toolbarEl) {
            toolbarEl.querySelectorAll('button[data-action]').forEach(button => {
                const action = button.dataset.action;
                const buttonData = buttons.find(b => b.icon === action && b.isActive);
                if (buttonData && buttonData.isActive()) {
                    button.classList.add('active', 'bg-secondary');
                    button.classList.remove('btn-outline-secondary');
                    button.classList.add('btn-secondary');
                } else {
                    button.classList.remove('active', 'bg-secondary');
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-outline-secondary');
                }
            });
        }

        if (editor) {
            editor.on('update', () => updateActiveButtons(toolbar));
            editor.on('selectionUpdate', () => updateActiveButtons(toolbar));
        }

        targetElement.appendChild(toolbar);
    }

    function htmlToMarkdown(html) {
        if (typeof TurndownService !== 'undefined') {
            const turndown = new TurndownService({
                headingStyle: 'atx',
                codeBlockStyle: 'fenced',
            });
            return turndown.turndown(html);
        }
        return html;
    }

    return {
        init: initEditor,
        getEditor: getEditor,
        getContent: getContent,
        setContent: setContent,
        getHTML: getHTML,
        destroy: destroy,
        createToolbar: createToolbar,
        htmlToMarkdown: htmlToMarkdown,
    };
})();