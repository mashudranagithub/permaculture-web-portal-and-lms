<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    height: {
        type: Number,
        default: 200
    }
});

const emit = defineEmits(['update:modelValue']);

const editorElement = ref(null);
let editorInstance = null;

onMounted(() => {
    if (window.ClassicEditor) {
        window.ClassicEditor
            .create(editorElement.value, {
                placeholder: props.placeholder,
                toolbar: [
                    'heading', '|', 
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 
                    'outdent', 'indent', '|', 
                    'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
                ]
            })
            .then(editor => {
                editorInstance = editor;

                // Configure Custom Upload Adapter
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new MyUploadAdapter(loader);
                };
                
                // Set initial content
                editor.setData(props.modelValue || '');

                // Height Adjustment
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', `${props.height}px`, editor.editing.view.document.getRoot());
                });

                // Handle changes
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    emit('update:modelValue', data);
                });
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });
    } else {
        console.error('CKEditor not found. Make sure the script is loaded in app.blade.php');
    }
});

// Custom Upload Adapter Class
class MyUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(file => new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('upload', file);

            axios.post(route('media.upload'), data, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                resolve({ default: response.data.url });
            })
            .catch(error => {
                reject(error.response?.data?.error?.message || 'Upload failed');
            });
        }));
    }

    abort() {
        // Handle abort if necessary
    }
}

onBeforeUnmount(() => {
    if (editorInstance) {
        editorInstance.destroy()
            .catch(error => {
                console.error('CKEditor destroy error:', error);
            });
    }
});

// Watch for external changes only
watch(() => props.modelValue, (newValue) => {
    if (editorInstance) {
        const currentData = editorInstance.getData();
        if (newValue !== currentData) {
            // We use a small timeout to ensure we don't interrupt the editor's internal state
            // especially during image uploads or complex operations.
            editorInstance.setData(newValue || '');
        }
    }
});
</script>

<template>
    <div class="ckeditor-wrapper">
        <div ref="editorElement"></div>
    </div>
</template>

<style>
.ck-editor__editable_inline {
    border-radius: 0 0 4px 4px !important;
}
.ckeditor-wrapper .ck-editor__main {
    min-height: v-bind(height + 'px');
}
.ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
    border-color: #dee2e6;
}
.ck.ck-editor__editable.ck-focused {
    border-color: #28a745 !important;
    box-shadow: none !important;
}
</style>
