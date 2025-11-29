<div 
    x-data="{
        editor: null,
        content: @entangle('content'),
        model: '{{ $model }}',
        placeholder: '{{ $placeholder }}',
        height: '{{ $height }}',
        toolbar: '{{ $toolbar }}',
        init() {
            // Initialize Quill editor
            this.editor = new Quill(`#{{ $editorId }}`, {
                theme: 'snow',
                placeholder: this.placeholder,
                modules: {
                    toolbar: this.getToolbarOptions()
                }
            });

            // Set initial content
            if (this.content) {
                this.editor.root.innerHTML = this.content;
            }

            // Handle text change with debounce
            let debounceTimer;
            this.editor.on('text-change', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const htmlContent = this.editor.root.innerHTML;
                    this.content = htmlContent;
                    
                    // Dispatch to Livewire - GUNAKAN DASH UNTUK KONSISTENSI
                    Livewire.dispatch('editor-content-updated', {
                        model: this.model,
                        content: htmlContent
                    });
                }, 500);
            });

            // Listen for refresh events
            Livewire.on('refreshQuill', (data) => {
                if (data.editorId === '{{ $editorId }}' && this.editor) {
                    this.editor.root.innerHTML = data.content || '';
                }
            });
        },
        getToolbarOptions() {
            if (this.toolbar === 'basic') {
                return [
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }]
                ];
            }
            
            // Full toolbar
            return [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
                ['link'],
                {{-- ['link', 'image', 'video'], --}}
                [{ 'align': [] }],
                ['clean']
            ];
        }
    }"
    wire:key="editor-{{ $editorId }}"
>
    <div id="{{ $editorId }}" style="height: {{ $height }};"></div>
    
    <!-- Hidden input untuk Livewire binding -->
    <input type="hidden" wire:model="content">
</div>
 