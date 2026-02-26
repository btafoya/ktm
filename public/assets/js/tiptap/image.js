// Simple Image extension for TipTap (vanilla JavaScript version)
window.ImageExtension = (function() {
    const { Node, mergeAttributes } = window.tiptapCore || window;

    return Node.create({
        name: 'image',

        group: 'block',

        inline: false,

        atom: true,

        addAttributes() {
            return {
                src: {
                    default: null,
                },
                alt: {
                    default: null,
                },
                title: {
                    default: null,
                },
                width: {
                    default: null,
                },
            };
        },

        parseHTML() {
            return [
                {
                    tag: 'img[src]',
                },
            ];
        },

        renderHTML({ HTMLAttributes }) {
            return ['img', mergeAttributes({ ...HTMLAttributes, class: 'img-fluid' })];
        },

        addCommands() {
            return {
                setImage: attributes => ({ commands }) => {
                    return commands.insertContent({
                        type: this.name,
                        attrs: attributes,
                    });
                },
            };
        },
    });
})();