<div wire:ignore>
    <div 
        id="{{ $editorId }}" 
        style="height: {{ $height }};"
        x-data="{
            quill: null,
            isInitialized: false,
            
            init() {
                if (this.isInitialized) return;
                
                if (typeof Quill === 'undefined') { 
                    return;
                }
                
                const toolbarOptions = [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'font': [] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'clean'],
                    {{-- ['image'] --}}
                ];
                
                this.quill = new Quill(this.$el, {
                    theme: 'snow',
                    placeholder: '{{ $placeholder }}',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });
 
                @if(!empty($content))
                    this.quill.root.innerHTML = {!! json_encode($content) !!};
                @endif
 
                let debounceTimer;
                this.quill.on('text-change', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const content = this.quill.root.innerHTML;  
                        Livewire.dispatch('editor-content-updated', {
                            model: '{{ $model }}',
                            content: content
                        });
                    }, 500);
                });

                this.isInitialized = true;
            }
        }"
        x-init="init()"
    ></div>
</div>