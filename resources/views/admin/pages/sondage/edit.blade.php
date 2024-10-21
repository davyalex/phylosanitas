@extends('admin.layout')
@section('title', 'Modifier un sondage ')

@section('content')
    <section class="section">

        <form class="needs-validation" action="{{ route('post.update-sondage', $post['id']) }}" method="POST"
            enctype="multipart/form-data" novalidate>
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">

                                <!-- General Form Elements -->
                                <div class="row">
                                    <div class="col-md-12 mb-3 d-none">
                                        <label class="form-label">Categorie</label>
                                        <select name="category" id="category"
                                            class="form-select  @error('category') is-invalid @enderror"
                                            aria-label="Default select example" required>
                                            {{-- <option disabled selected></option> --}}
                                            @foreach ($category as $item)
                                                <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <div class="invalid-feedback">Veuillez sélectionner une categorie</div>

                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="inputNumber" class="form-label">Image de presentation</label>
                                        <input class="form-control" name="image" type="file" id="formFile">
                                    </div>

                                    <div>
                                        <img src="{{ $post->getFirstMediaUrl('image') }}" alt="sondage image"
                                            width="50px">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div class="col-lg-12" id="description">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Posez votre question</h5>

                                <!-- TinyMCE Editor -->
                                <textarea name="description" id="tinymce-editor">
          {{ $post->description }}
          </textarea><!-- End TinyMCE Editor -->

                            </div>
                        </div>

                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <div class="row mb-3" id="optionSondage">
                                    <label for="inputNumber" class="form-label fw-bold">Définissez les options de
                                        reponse </label>
                                    <table class="table table-bordered" id="dynamicAddRemove" style="border:white">
                                        <tr>
                                            {{-- <td class="col-md-10"><input type="text" name="option[0][title]"
                                                    placeholder="" class="form-control" />
                                                <div class="invalid-feedback">Veuillez remplir ce champs</div>
                                            </td> --}}
                                            <td class="col-md-12"></td>
                                        </tr>
                                    </table>

                                </div>
                                <button type="button" name="add" id="add-btn" class="btn btn-success">+
                                    Cliquer pour ajouter des reponses</button>

                            </div>
                        </div>

                    </div>

                    <input type="text" value="sondage" name="sondage" hidden>

                    <div class="col-ld-12">
                        <button type="submit" class="btn btn-primary w-100">Valider</button>
                    </div>


                    <script type="text/javascript">
                        $(document).ready(function() {
                            tinymce.init({
                                selector: 'textarea#tinymce-editor',
                                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                                imagetools_cors_hosts: ['picsum.photos'],
                                menubar: 'file edit view insert format tools table help',
                                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                                toolbar_sticky: true,
                                autosave_ask_before_unload: true,
                                autosave_interval: '30s',
                                autosave_prefix: '{path}{query}-{id}-',
                                autosave_restore_when_empty: false,
                                autosave_retention: '2m',
                                image_advtab: true,
                                link_list: [{
                                        title: 'My page 1',
                                        value: 'https://www.tiny.cloud'
                                    },
                                    {
                                        title: 'My page 2',
                                        value: 'http://www.moxiecode.com'
                                    }
                                ],
                                image_list: [{
                                        title: 'My page 1',
                                        value: 'https://www.tiny.cloud'
                                    },
                                    {
                                        title: 'My page 2',
                                        value: 'http://www.moxiecode.com'
                                    }
                                ],
                                image_class_list: [{
                                        title: 'None',
                                        value: ''
                                    },
                                    {
                                        title: 'Some class',
                                        value: 'class-name'
                                    }
                                ],
                                importcss_append: true,
                                file_picker_callback: function(callback, value, meta) {
                                    /* Provide file and text for the link dialog */
                                    if (meta.filetype === 'file') {
                                        callback('https://www.google.com/logos/google.jpg', {
                                            text: 'My text'
                                        });
                                    }

                                    /* Provide image and alt text for the image dialog */
                                    if (meta.filetype === 'image') {
                                        callback('https://www.google.com/logos/google.jpg', {
                                            alt: 'My alt text'
                                        });
                                    }

                                    /* Provide alternative source and posted for the media dialog */
                                    if (meta.filetype === 'media') {
                                        callback('movie.mp4', {
                                            source2: 'alt.ogg',
                                            poster: 'https://www.google.com/logos/google.jpg'
                                        });
                                    }
                                },
                                templates: [{
                                        title: 'New Table',
                                        description: 'creates a new table',
                                        content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                                    },
                                    {
                                        title: 'Starting my story',
                                        description: 'A cure for writers block',
                                        content: 'Once upon a time...'
                                    },
                                    {
                                        title: 'New list with dates',
                                        description: 'New List with dates',
                                        content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                                    }
                                ],
                                template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
                                template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
                                height: 300,
                                image_caption: true,
                                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                                noneditable_noneditable_class: 'mceNonEditable',
                                toolbar_mode: 'sliding',
                                contextmenu: 'link image imagetools table',
                                // skin: useDarkMode ? 'oxide-dark' : 'oxide',
                                // content_css: useDarkMode ? 'dark' : 'default',
                                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
                            });

                        })

                        //afficher les reponse du sondage recuperer
                        var reponses = @json($reponseSondage);

                        //faire un boucle en fonction de reponse pour afficher les reponse
                        $.each(reponses, function(index, value) {
                            $("#dynamicAddRemove").append('<tr><td><input type="text" name="option[' + index +
                                '][title]" value="' + value.title +
                                '" class="form-control" required /><div class="invalid-feedback">Veuillez remplir ce champs</div>           </td><td><button type="button" class="btn btn-danger remove-tr"><i class="bi bi-trash text-white"></i></button></td></tr>'
                            );
                        });




                        var i = parseInt(reponses.length);
                        $("#add-btn").click(function() {
                            ++i;
                            $("#dynamicAddRemove").append('<tr><td><input type="text" name="option[' + i +
                                '][title]" placeholder="" class="form-control" required /><div class="invalid-feedback">Veuillez remplir ce champs</div>           </td><td><button type="button" class="btn btn-danger remove-tr"><i class="bi bi-trash text-white"></i></button></td></tr>'
                            );

                        });
                        $(document).on('click', '.remove-tr', function() {
                            $(this).parents('tr').remove();
                        });


                     
                    </script>

                </div>
            </div>
        </form>





    </section>

@endsection
