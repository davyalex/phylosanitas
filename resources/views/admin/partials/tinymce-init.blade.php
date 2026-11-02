<script>
    $(document).ready(function () {
        tinymce.init({
            selector: '{{ $selector ?? "textarea.tinymce-editor" }}',
            plugins: 'preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | charmap emoticons | fullscreen preview print | insertfile image media link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            height: 400,
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            image_advtab: true,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            toolbar_mode: 'sliding',
            content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.7; }',
            images_upload_url: '{{ route("post.upload-image") }}' + '?post_id={{ $postId ?? "" }}',
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '{{ route("post.upload-image") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                xhr.onload = function () {
                    var json;
                    if (xhr.status === 403) { failure('HTTP Error: ' + xhr.status, { remove: true }); return; }
                    if (xhr.status < 200 || xhr.status >= 300) { failure('HTTP Error: ' + xhr.status); return; }
                    json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location != 'string') { failure('Invalid JSON: ' + xhr.responseText); return; }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                @isset($postId)
                formData.append('post_id', '{{ $postId }}');
                @endisset
                xhr.send(formData);
            },
        });
    });
</script>
