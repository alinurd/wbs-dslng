<div wire:ignore>
    <div 
        id="{{ $editorId }}" 
        style="height: {{ $height }};"
        x-data="{
            quill: null,
            isInitialized: false,
            
            init() {
                 if (this.isInitialized) {
                    return;
                }
                
                if (typeof Quill === 'undefined') {
                    console.error('Quill.js not loaded. Please include Quill.js in your layout.');
                    return;
                }
                
                const toolbarOptions = this.getToolbarOptions();
                
                this.quill = new Quill(`#${this.$el.id}`, {
                    theme: 'snow',
                    placeholder: '{{ $placeholder }}',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                 if (@this.content) {
                    this.quill.root.innerHTML = @this.content;
                }

                 this.quill.on('text-change', () => {
                    const content = this.quill.root.innerHTML;
                    @this.set('content', content);
                });

                 this.$wire.on('clear-editor', () => {
                    this.quill.root.innerHTML = '';
                });

                this.isInitialized = true;
            },
            
            getToolbarOptions() {
                const toolbars = {
                    'full': [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'font': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ],
                    'basic': [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link'],
                        ['clean']
                    ],
                    'minimal': [
                        ['bold', 'italic'],
                        [{ 'list': 'bullet' }],
                        ['clean']
                    ]
                };
                
                return toolbars['{{ $toolbar }}'] || toolbars['full'];
            }
        }"
        x-init="init()"
    ></div>
</div>