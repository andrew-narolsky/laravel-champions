import tinymce from 'tinymce/tinymce';

import 'tinymce/models/dom';

// theme + icons
import 'tinymce/themes/silver';
import 'tinymce/icons/default';

// plugins
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/table';
import 'tinymce/plugins/code';

// styles
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/skins/content/default/content.min.css';

class TextEditor {

    static init(root = document) {
        const textareas = root.querySelectorAll('textarea.text-editor');

        textareas.forEach((textarea) => {
            if (textarea.dataset.editorInitialized) {
                return;
            }

            if (!textarea.id) {
                textarea.id = 'editor_' + Math.random().toString(36).slice(2);
            }

            tinymce.init({

                license_key: 'gpl',
                skin: false,
                content_css: false,

                target: textarea,
                height: 300,
                menubar: false,
                branding: false,

                plugins: ['lists', 'link', 'image', 'table', 'code'],
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table |  code',
                content_style: `body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto; font-size: 14px; }`,

                setup(editor) {
                    textarea.dataset.editorInitialized = '1';

                    editor.on('change keyup', () => {
                        editor.save();
                    });
                }
            });
        });
    }

    static destroy(root = document) {
        root.querySelectorAll('textarea.text-editor').forEach((textarea) => {
            if (textarea.id) {
                tinymce.get(textarea.id)?.remove();
            }

            textarea.removeAttribute('data-editor-initialized');
        });
    }
}

window.TextEditor = TextEditor;
