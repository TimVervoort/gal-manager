<?php
//Laat mogelijke fouten zien in de uitvoer, hierdoor zal de JSON wel fout geparsed worden.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('settings.php');

if (!isLoggedIn()) {
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <title>Gallery Manager</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <style type="text/css">
      @import url('https://fonts.googleapis.com/css?family=Roboto');
      html, body {font-family: 'Roboto', sans-serif;height:100%;width:100%;margin:0;padding:0;overflow:hidden;background:#111;color:#fff;text-align:center;font-weight:300;display:flex;flex-direction:column;justify-content:center;}
      form {width:100%;height:auto;overflow:auto;marign:0;padding:0;}
      input {box-sizing:border-box;width:90%;max-width:400px;margin:20px auto;text-align:center;border:0;background:#eee;color:#123;clear:both;display:block;overflow:hidden;height:50px;line-height:50px;}
      a:link, a:visited {color:#fff;text-decoration:underline;}
    </style>
  </head>
  <body>
    <h1>Login to edit galleries</h1>
    <form action="index.php" method="post">
      <input type="text" name="user" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="submit" value="Login" />
    </form>
    <p>&copy; <a href="https://www.timvervoort.com">Tim Vervoort</a></p>
  </body>
</html>
<?php
    die();
}
require_once('manager.php');
$manager = new GalleryManager();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Gallery Manager</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <style type="text/css">
      @import url('https://fonts.googleapis.com/css?family=Roboto');
      html, body {
        font-family: 'Roboto', sans-serif;
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        color: #fff;
        background: #111;
        text-align: center;
        overflow: auto;
      }
      a:link, a:visited {
        color: #fff;
        text-decoration: underline;
      }
      *::-webkit-scrollbar-track {
        border-radius: 2px;
        background-color: #000;
      }
      *::-webkit-scrollbar {
        width: 2px;
        background-color: #000;
      }
      *::-webkit-scrollbar-thumb {
        border-radius: 2px;
        background-color: #fff;
      }
      input, input[type="text"], input[type="file"] {
        border-sizing: border-box;
        border: none;
      }
      ul {
        list-style-type: none;
        padding: 0;
        width: 100%;
        max-width: 1680px;
        margin: 0 auto;
        overflow: auto;
        text-align: center;
        display: block;
        height: auto;
      }
      li {
        width: 220px;
        height: 220px;
        float: left;
        position: relative;
        margin: 10px;
        overflow: auto;
      }
      li .image, li form {
        width: 200px;
        height: 200px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
        position: absolute;
        top: 0;
        left: 0;
        border: 10px solid #fff;
      }
      input[type="file"] {
        height: 200px;
        width: 200px;
        border: none;
        background: #111;
        color: #fff;
        background: #fff;
        background-image: url('icons/upload.svg');
        background-size: 100px 100px;
        background-position: center center;
        background-repeat: no-repeat;
      }
      input[type="file"]:hover {
        cursor: pointer;
      }
      @media screen and (max-width: 482px) {
        li { width: 100%; }
        li .image, li form, input[type="file"] { width: calc(100% - 40px); }
      }
      input[type="file"]::-webkit-file-upload-button {
        visibility: hidden;
      }
      li .delete, li .deleteDisk {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 25px;
        height: 25px;
        opacity: 0.5;
        filter: alpha(opacity=50);
        z-index: 999;
      }
      li .delete:hover, li .deleteDisk:hover {
        opacity: 0.8;
        filter: alpha(opacity=0.8);
        cursor: pointer;
        top: -10px;
        right: -10px;
        width: 220px;
        height: 220px;
        background: rgba(226, 27, 27, 0.8);
      }
      li .to-left, li .to-right {
          width: 25px;
          height: 25px;
          text-align: center;
          line-height: 25px;
          color: #fff;
          background: rgba(20, 20, 20, 0.5);
          top: calc(200px / 2 - 25px / 2);
          position: absolute;
          z-index: 999;
          display: block;
      }
      li .to-left {
          left: 0;
      }
      li .to-right {
          right: 0;
      }
      .progress {
        width: 100%;
        max-width: 1660px;
        margin: 20px auto;
      }
      .progress .bar {
        display: block;
        clear: both;
        width: 0;
        height: 20px;
        background: #4ca95b;
        margin: 10px 0;
      }
      .notifications {
        width: 260px;
        top: 0;
        right: 0;
        position: fixed;
        z-index: 9999;
        height: 100vh;
        max-height: 100vh;
        overflow: auto;
      }
      .notification {
        color: #fff;
        width: 200px;
        border-radius: 10px;
        padding: 20px;
        margin: 10px;
        text-align: left;
      }
      .green {
        background: #4ca95b;
      }
      .red {
        background: #e21b1b;
      }

      .gallery li {
        width: 100%;
        height: auto;
        margin: 20px 0;
        clear: both;
        max-height: unset;
        max-width: unset;
        overflow: auto;
      }
      .gallery li h2 {display:block;overflow:auto;width:calc(80% - 50px);padding:0 20px;margin: 0 0 10px 10px;height:80px;line-height:80px;font-size:30px;background:#fff;color:#111;float:left;}
      .gallery li .deleteGallery {display:block;overflow:auto;width:calc(20% - 10px);height:80px;float:left;clear:right;margin:0 10px 10px 0;line-height:80px;font-size:30px;color:#fff;}
      .gallery li .deleteGallery:hover {cursor:pointer;}
      .gallery li p {margin: 20px auto;}
      .gallery li input[type="text"], .newGallery {
        height: 80px;
        line-height: 80px;
        font-size: 30px;
        float: left;
        display: block;
        color: #111;
      }
      .gallery li input[type="text"] {
        width: calc(80% - 50px);
        margin: 0 0 0 10px;
        padding: 0 20px;
        background: #fff;
      }
      .newGallery {
        width: calc(20% - 10px);
        margin: 0 10px 0 0;
        color: #fff;
      }
      .newGallery:hover, .deleteGallery:hover {
        background: #ccc;
        cursor: pointer;
      } 
      .gallery li ul {
        width: 100%;
        overflow: auto;
        display: block;
        height: auto;
      }
      .gallery li ul li {
        width: 220px;
        height: 220px;
        float: left;
        position: relative;
        margin: 10px;
        clear: none;
      }

      #menu {
        z-index: 99999;
        background: #fff;
        color: #111;
        padding: 10px;
        overflow: auto;
        position: absolute;
        top: 0;
        left: 0;
        display: none;
      }
      #menu a {
        color: #000;
      }

    </style>
  </head>
  <body>

    <h1>All available images for this website</h1>
    <p>If you delete an image from this list of all available images, the image will be deleted from all galleries and be removed from the server.</p>

    <div class="progress">
      <div class="status"></div>
      <div class="bar"></div>
    </div>

    <ul class="all">
      <li id="uploader" title="Click to upload images to your website.">
        <form action="upload.php" method="post" enctype="multipart/form-data">
          <input type="file" name="file[]" multiple />
        </form>       
      </li>
      <?php
        $images = scandir($thumbnails);
        foreach ($images as $i) {
          if ($i == '.' || $i == '..') { continue; }
          echo '<li><div class="image" style="background-image:url(\''.$thumbnails.$i.'\');"><img class="deleteDisk" id="'.$i.'" src="icons/delete.svg" alt="Delete image" title="Delete image from server and all galleries." /></div></li>';
        }
      ?>
    </ul>

    <ul class="gallery">
      <li>
        <input type="text" class="newGallery" placeholder="Create new gallery" />
        <div class="newGallery green" value="Create gallery" title="Click to create a new empty gallery with this name.">create</div>
      </li>
      <?php
      $gals = $manager->getGalleries();
      foreach ($gals as $gal) {
        echo '<li id="'.$gal['id'].'"><h2>'.$gal['name'].'</h2>';
        echo '<div class="deleteGallery red" id="'.$gal['id'].'" value="Delete gallery" title="Delete this gallery but keep all the images on the server.">delete gallery</div>';
        echo '<p>Click the red cross to delete the image from the gallery but not from the server.</p><ul>';
        $imgs = $manager->getGalleryContents($gal['id']);
        foreach ($imgs as $img) {
          echo '<li data-url="'.$img['img'].'"><div class="image" style="background-image:url('.$thumbnails.$img['img'].');"><img class="delete" id="'.$img['img'].'" src="icons/delete.svg" alt="Delete image" title="Delete image from this gallery but not from the server." /></div></li>';
        }
        echo '</ul></li>';
      }
      ?>
    </ul>

    <p>Icons made by <a href="https://www.flaticon.com/authors/alfredo-hernandez" title="Alfredo Hernandez">Alfredo Hernandez</a> &amp; <a href="https://www.flaticon.com/authors/pixel-buddha" title="Pixel Buddha">Pixel Buddha</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>, licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></p>
    <p>Web Gallery Manager by <a href="https://www.timvervoort.com">Tim Vervoort</a></p>

    <div class="notifications"></div>
    <div id="menu"></div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://malsup.github.io/jquery.form.js"></script> 
    <script type="text/javascript">

      function uniqId() {
        return Math.round(new Date().getTime() + (Math.random() * 100));
      }

      function notification(text, color) {
        var id = uniqId();
        $('.notifications').append('<div class="notification '+color+'" id="'+id+'">'+text+'</div>');
        $('#'+id).delay(5000).fadeOut('slow');
        $('.notification').click(function() { $(this).fadeOut(); });
      }

      $('body').on('click', '.image', function(e) {
         var img = $(this).find('.delete').attr('id');
         if (!img) { img = $(this).find('.deleteDisk').attr('id'); }
         //window.open('<?php echo $uploads; ?>'+img, '_blank');
      });

      $('body').on('click', '.deleteDisk', function() {
          var img = $(this).attr('id');
          $(this).parent().fadeOut().remove();
          $.ajax({
            type: 'POST',
            url: 'delete.php?img='+img,
            dataType: "html",
            success: function(data) {
              notification(data, 'red');
            },
            error: function() {
              notification('Error. Try again.', 'red');
            }
          });
          console.log('Deleted from disk, delete from galleries TODO');
        });

      $('body').on('click', '.delete', function() {
          var img = $(this).attr('id');
          var gal = $(this).parent().parent().parent().parent().attr('id');
          $(this).parent().fadeOut().remove();
          $.ajax({
            type: 'POST',
            url: 'manager.php?delete&img='+img+'&gal='+gal,
            dataType: "html",
            success: function(data) {
              notification(data, 'red');
            },
            error: function() {
              notification('Error. Try again.', 'red');
            }
          });
          console.log('Delete ' + img + ' from gallery ' + gal);
        });

      //Start uploading after select
      $('input[type=file]').change(function() { 
        $('form').submit(); 
      });

      var percent = $('.status');
      $('form').ajaxForm({
        beforeSend: function() {
          var percentVal = '0%';
          $('.progress .bar').css('width', percentVal);
          percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
          var percentVal = 'Uploading: '+percentComplete+'% complete.';
          percent.html(percentVal);
          $('.progress .bar').css('width', percentComplete+'%');
        },
        complete: function(xhr) {
          var res = $.parseHTML(xhr.responseText);
          jQuery.each(res, function(i, k) {
            if ($(k).hasClass('success')) {
              var img = '<li><div class="image" style="background-image:url(thumbs/' + $(k).text() + ');"><img class="deleteDisk" id="' + $(k).text() + '" src="icons/delete.svg" alt="Delete image" /></div></li>';
              $(img).insertAfter('#uploader');
              notification('Image ' + $(k).text() + ' uploaded!', 'green');
            }
            else {
              notification('Could not upload ' + $(k).text(), 'red');
            }
          });
          percent.html('');
          $('.progress .bar').css('width', '0%');
        }
      });

      var mouseX;
      var mouseY;
      $(document).mousemove(function(e) {
        mouseX = e.pageX; 
        mouseY = e.pageY;
      });  

      $('body').click(function(e) {
        if ($(e.target).hasClass('toGallery')) { return; }
        if ($('#menu').css('display') == 'block') { $('#menu').fadeOut(); }
      });

      $('body').on('contextmenu', 'ul.all li', function(e) {
        var pieces = $(e.target).css('background-image').replace('url("', '').replace('")', '').split('/');
        var file = pieces[pieces.length-1];
        <?php
        $options = '';
        foreach ($manager->getGalleries() as $gal) {
           $options .= '<option value="'.$gal['id'].'">'.$gal['name'].'</option>';
        }
        ?>
        $('#menu').html('<p><a href="<?php echo $uploads; ?>'+file+'" target="_blank">'+file+'</a></p><p>Add to album:</p><select data-img="'+file+'" class="toGallery"><option value="-1">Copy to?</option><?php echo $options; ?></select></div>');
        $('#menu').css({'top':mouseY, 'left':mouseX}).fadeIn();
        return false;
      });

      $('body').on('click', 'div.newGallery', function() {
        var name = $('input.newGallery').val();
        console.log('Creating new empty gallery: '+name);
        $.ajax({
         type: 'POST',
         url: 'manager.php?create&name='+name,
         dataType: "html",
         success: function(data) {
           notification(data, 'red');
         },
         error: function() {
           notification('Error. Try again.', 'red');
         }
       });
      });

     $('body').on('click', '.deleteGallery', function() {
       var id = $(this).attr('id');
       console.log('Deleting gallery: '+id);
       $.ajax({
         type: 'POST',
         url: 'manager.php?delete&gal='+id,
         dataType: "html",
         success: function(data) {
           notification(data, 'red');
         },
         error: function() {
           notification('Error. Try again.', 'red');
         }
       });
       $('ul.gallery li#'+id).fadeOut();
     });

     $('body').on('change', '.toGallery', function() {
       console.log('copy to gallery');
       var gal = $('.toGallery').val();
       var img = $('.toGallery').data('img');
       console.log('Add '+img+' to album: '+gal);
       $.ajax({
         type: 'POST',
         url: 'manager.php?add&img='+img+'&gal='+gal,
         dataType: "html",
         success: function(data) {
           notification(data, 'red');
         },
         error: function() {
           notification('Error. Try again.', 'red');
         }
       });
     });

     $('ul.gallery li ul li div.image').each(function(i, v) {
        var prev = $(v).parent().prev().data('url');
        var next = $(v).parent().next().data('url');
        $(v).append('<div class="to-left" data-left="'+prev+'">&lt;</div>');
        $(v).append('<div class="to-right" data-right="'+next+'">&gt;</div>');
     });

     $('body').on('click', '.to-left', function(e) {
         var imgA = $(e.currentTarget).parent().parent().prev();
         var imgB = $(e.currentTarget).parent().parent();
         var gal = imgA.parent().parent().attr('id');
         var imgAid = imgA.attr('data-url');
         var imgBid = imgB.attr('data-url');
         if (!imgAid || !imgBid || !gal) { return; }
         var temp = imgA.html();
         imgA.html(imgB.html());
         imgB.html(temp);
         console.log('Change order '+imgAid+' with '+imgBid+' in gallery '+gal);
         $.ajax({
             type: 'POST',
             url: 'manager.php?switch&gal='+gal+'&imgA='+imgAid+'&imgB='+imgBid,
             dataType: 'html',
             success: function(data) {
                 notification('Changed order of images.', 'green');
             },
             error: function() {
                 notification('Error. Try again.', 'red');
             }
         });
      });

      $('body').on('click', '.to-right', function(e) {
          var imgA = $(e.currentTarget).parent().parent();
          var imgB = $(e.currentTarget).parent().parent().next();
          var gal = imgA.parent().parent().attr('id');
          var imgAid = imgA.attr('data-url');
          var imgBid = imgB.attr('data-url');
          if (!imgAid || !imgBid || !gal) { return; }
          var temp = imgA.html();
          imgA.html(imgB.html());
          imgB.html(temp);
          console.log('Change order '+imgAid+' with '+imgBid+' in gallery '+gal);
          $.ajax({
              type: 'POST',
              url: 'manager.php?switch&gal='+gal+'&imgA='+imgAid+'&imgB='+imgBid,
              dataType: 'html',
              success: function(data) {
                  notification('Changed order of images.', 'green');
              },
              error: function() {
                  notification('Error. Try again.', 'red');
              }
          });
      });

    </script>

  </body>
</html>