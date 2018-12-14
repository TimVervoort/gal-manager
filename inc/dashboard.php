<div class="progress">
    <div class="bar">&nbsp;</div>
</div>

<form name="newGallery" class="newGallery" method="post">
    <input type="text" name="name" class="name" placeholder="Naam nieuw album" />
    <input type="submit" value="Nieuw album" />
</form>

<ul id="galleries">
    <!-- Display galleries already present -->
    <?php
        $gals = json_decode(file_get_contents($SETTINGS->managerUrl.'inc/handler.php?all'))->galleries;
        foreach ($gals as $g) {
          echo '<li data-id="'.$g->id.'" data-name="'.$g->name.'">';
          echo '  <h2 class="name">'.$g->name.'</h2>';
          echo '  <span class="btn rename">Rename</span>';
          echo '  <span class="btn delete">Delete</span>';
          echo '  <ul class="images">';
          echo '    <li class="uploader" title="Browse images or video\'s on your device to upload.">';
          echo '      <form name="upload" class="upload" action="inc/handler.php" method="post" enctype="multipart/form-data">';
          echo '        <input type="hidden" name="uploadTo" value="'.$g->id.'" />';
          echo '        <input type="file" name="file[]" multiple />';
          echo '      </form>';
          echo '    </li>';
          $imgs = $g->content;
          foreach ($imgs as $i) {
            if (isset($i->vid)) {
              echo '    <li data-img="'.$i->vid.'" style="width:calc(200px * 16 / 9);"><video controls style="width:calc(200px * 16 / 9);height:200px;overflow:hidden;"><source src="uploads/'.$i->vid.'" type="video/'.explode('.', $i->vid)[sizeof(explode('.', $i->vid)) - 1].'" /></video></li>';
            }
            else if (isset($i->iframe)) {
              echo '    <li data-img="'.$i->iframe.'" style="width:calc(200px * 16 / 9);"><iframe style="width:calc(200px * 16 / 9);height:200px;overflow:hidden;border:0;margin:0;padding:0;" src="'.$i->iframe.'"></iframe></li>';
            }
            else {
              echo '    <li data-img="'.$i->img.'" style="background-image:url(\'thumbs/'.$i->img.'\');">&nbsp;</li>';
            }
          }
          echo '    <section><input type="text" class="url" placeholder="Add by URL..." /><span class="btn addUrl">Add</span></section>';
          echo '  </ul>';
          echo '</li>';
        }
    ?>
</ul>

<a href="?logout">Logout</a>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://malsup.github.io/jquery.form.js"></script><!-- File uploader -->
<script type="text/javascript">

    // Display uploaded images in their gallery
    function handler(json) {
        // Uploading went wrong, display error message returned by server
        if (json.error) {
            alert(json.error);
            console.log('%cUpload error.', 'color:red;');
        }
        // Uploading succeeded, display uploaded dimages in gallery
        else if (json.images) {
            $.each(json.images, function(i, v) {
                if (v.includes('http')) {
                    $('ul#galleries li[data-id=\''+json.gallery+'\'] ul.images').append('<li data-img="'+v+'" style="width:calc(200px * 16 / 9);"><iframe style="width:calc(200px * 16 / 9);height:200px;overflow:hidden;border:0;margin:0;padding:0;" src="'+v+'"></iframe></li>');
                }
                else if (v.includes('.mp4') || v.includes('.webm')) {
                    $('ul#galleries li[data-id=\''+json.gallery+'\'] ul.images').append('<li data-img="'+v+'" style="width:calc(200px * 16 / 9);"><video controls style="width:calc(200px * 16 / 9);height:200px;overflow:hidden;"><source src="gallery/<?php echo $SETTINGS->originalDir; ?>'+v+'" type="video/'+v.split('.')[v.split('.').length - 1]+'"></video></li>');
                }
                else {
                    $('ul#galleries li[data-id=\''+json.gallery+'\'] ul.images').append('<li data-img="'+v+'" style="background-image:url(\'gallery/<?php echo $SETTINGS->thumbDir; ?>'+v+'\');">&nbsp;</li>');
                }
            });
            console.log('%cUpload complete.', 'color:green;');
            initAllImageButtons();
        }
    }

    // Active all image uploader file selectors (and dynamicly added ones)
    $(document).on('change', 'input[type=file]', function(e) {
        $(e.currentTarget).parent().submit(); // When files are selected, start uploading
        console.log('%cFile selection complete. Start uploading.', 'color:green;');
    });

    // Active all image uplaodes
    function initUploadingAllGalleries() {
        // Upload multiple images
        $('form.upload').ajaxForm({
          beforeSend: function() {
            $('.progress .bar').css('width', '0%');
          },
          uploadProgress: function(event, position, total, percentComplete) {
            $('.progress .bar').css('width', percentComplete+'%');
          },
          complete: function(xhr) {
            $('.progress .bar').css('width', '0%');
            handler(JSON.parse(xhr.responseText));
          }
        });
        console.log('%cAll uploaders ready!', 'color:green;');
        initAllImageButtons();
    }

    function initAllImageButtons() {
        $('ul#galleries ul li:not(.uploader) .delete').remove();
        $('ul#galleries ul li:not(.uploader)').append('<span class="delete" title="Delete this.">x</span>');
        $('ul#galleries ul li:not(.uploader):not(:nth-of-type(2))').append('<span class="prev" title="Swap with next.">&lt;</span>');
        $('ul#galleries ul li:not(.uploader):not(:last-of-type)').append('<span class="next" title="Swap with previous.">&gt;</span>');
        console.log('%cAll image delete buttons ready!', 'color:green;')
    }

    function handleSwapping(a, b) {
        var imgA = a.attr('data-img');
        var imgB = b.attr('data-img');
        var gal = b.parent().parent().attr('data-id');
        $.ajax({
            type: 'POST',
            url: 'inc/handler.php',
            data: {
                swapA: imgA,
                swapB: imgB,
                gallery: gal
            },
            success: function() {
                console.log('%cImages swapped.', 'color:green;');
            }
        });
        a.attr('data-img', imgB);
        b.attr('data-img', imgA);
        a.css('background-image', 'url(\'gallery/<?php echo $SETTINGS->thumbDir; ?>'+imgB+'\')');
        b.css('background-image', 'url(\'gallery/<?php echo $SETTINGS->thumbDir; ?>'+imgA+'\')');
    }

    $(document).on('click', 'ul#galleries ul li span.prev', function(e) {
        var other = $(e.currentTarget).parent().prev();
        var cur = $(e.currentTarget).parent();
        handleSwapping(other, cur);
    });

    $(document).on('click', 'ul#galleries ul li span.next', function(e) {
        var other = $(e.currentTarget).parent().next();
        var cur = $(e.currentTarget).parent();
        handleSwapping(other, cur);
    });

    $(document).on('click', 'ul#galleries ul .addUrl', function(e) {
        var url = $(e.currentTarget).prev().val();
        $(e.currentTarget).prev().val(''); // Clear url
        var gal = $(e.currentTarget).parent().parent().parent().attr('data-id');
        $.ajax({
            type: 'POST',
            url: 'inc/handler.php',
            data: {
                addUrl: url,
                gallery: gal
            },
            success: function(v) {
                console.log('%cAdded by url.', 'color:green;');
                if (v.includes('http')) {
                    $(e.currentTarget).parent().parent().append('<li data-img="'+v+'" style="width:calc(200px * 16 / 9);"><iframe style="width:calc(200px * 16 / 9);height:200px;overflow:hidden;border:0;margin:0;padding:0;" src="'+v+'"></iframe></li>');
                }
                else if (v.includes('.mp4') || v.includes('.webm')) {
                    $(e.currentTarget).parent().parent().append('<li data-img="'+v+'" style="width:calc(200px * 16 / 9);"><video controls style="width:calc(200px * 16 / 9);;height:200px;overflow:hidden;"><source src="gallery/<?php echo $SETTINGS->originalDir; ?>'+v+'" type="video/'+v.split('.')[v.split('.').length - 1]+'"></video></li>');
                  }
                else {
                    $(e.currentTarget).parent().parent().append('<li data-img="'+v+'" style="background-image:url(\'gallery/<?php echo $SETTINGS->thumbDir; ?>'+v+'\');">&nbsp;</li>');
                }
                initAllImageButtons();
            }
        });
    });

    $(document).on('click', 'ul#galleries ul li:not(.uploader) .delete', function(e) {
        var img = $(e.currentTarget).parent().attr('data-img');
        var gal = $(e.currentTarget).parent().parent().parent().attr('data-id');
        console.log('Deleting ' + img + ' from ' + gal + '...');
        $.ajax({
            type: 'POST',
            url: 'inc/handler.php',
            data: {
                delete: img,
                gallery: gal
            },
            success: function() {
                console.log('%cImage deleted.', 'color:green;');
            }
        });
        $(e.currentTarget).parent().fadeOut().delay(100).remove();
    });

    // Active rename gallery buttons (and dynamicly added ones)
    $(document).on('click', '.rename', function(e) {
        var txt = $(e.currentTarget).parent().find('.name').text();
        $(e.currentTarget).parent().find('.name').replaceWith('<input class="renamed" data-old="'+txt+'" name="name" value="'+txt+'" />').focus();
        console.log('%cStart renaming gallery.', 'color:green;');
    });

    // Active delete gallery buttons (and dynamicly added ones)
    $(document).on('click', '.delete', function(e) {
        var gal = $(e.currentTarget).parent().attr('data-id');
        $.ajax({
            type: 'POST',
            url: 'inc/handler.php',
            data: {
                deleteGallery: gal
            },
            success: function() {
                $(e.currentTarget).parent().fadeOut().delay(100).remove();
                console.log('%cGallery deleted.', 'color:green;');
            }
        });
    });

    // Active enter button on rename field (and dynamicly adde ones)
    $(document).on('keypress', '.renamed', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'inc/handler.php',
                data: {
                    rename: $(e.currentTarget).parent().attr('data-id'),
                    new: $(e.currentTarget).val() 
                },
                success: function() {
                    console.log('%cRenamed gallery.', 'color:green;');
                }
            });
            // Update name visible and in the model
            $(e.currentTarget).parent().attr('data-name', $(e.currentTarget).val());
            $(e.currentTarget).replaceWith('<h2 class="name">'+$(e.currentTarget).val()+'</h2>');
        }
    });

    $('form.newGallery').submit(function(e) {
        e.preventDefault();
        var name = $('form.newGallery .name').val();
        var names = [];
        $.each($('ul#galleries > li'), function(i, v) { names.push($(v).attr('data-name')); }); // Get all taken gallery names
        if (name == '' || $.inArray(name, names) > -1) { console.log('%cInvalid name.', 'color:red;'); return; }
        $.ajax({
            type: 'POST',
            url: 'inc/handler.php',
            data: {
                newGallery: name
            },
            success: function(res) {
                var id = JSON.parse(res)['id'];
                // Make new gallery visible
                $('form.newGallery .name').val('');
                $('#galleries').append('<li data-id="'+id+'" data-name="'+name+'"><h2 class="name">'+name+'</h2><span class="rename btn">Rename</span><span class="delete btn">Delete</span><ul class="images"><li class="uploader" data-name="'+name+'" title="Browse images or video\'s on your device to upload."><form name="upload" class="upload" action="inc/handler.php" method="post" enctype="multipart/form-data"><input type="hidden" name="uploadTo" value="'+id+'" /><input type="file" name="file[]" multiple /></form></li></ul></li>')
                $('form.upload').ajaxFormUnbind(); // Turn of previous binding to prevent double binding
                initUploadingAllGalleries(); // Init all visible galleries
                console.log('%cNew gallery created.', 'color:green;');
            }
        });
    });

    initUploadingAllGalleries(); // Init already present galleries
  
</script>