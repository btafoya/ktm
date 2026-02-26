
(function() {
    // Extract what we need from the bundle
    const bundle = TiptapBundle.default || TiptapBundle;
    const { Editor, Extension, Node, Mark, mergeAttributes, wrappingInputRule } = bundle;

    // Expose TipTap Core globally
    window.tiptapCore = {
        Editor,
        Extension,
        Node,
        Mark,
        mergeAttributes,
        wrappingInputRule
    };

    // Export individual classes as globals for editor.js
    window.Editor = Editor;
    window.StarterKit = bundle.StarterKit;
    window.TaskList = bundle.TaskList;
    window.TaskItem = bundle.TaskItem;

    console.log('TipTap loaded successfully');
})();
