<?php
    //https://ssl.dotpay.eu/?id=67969&amount=25&description=Oplata%20za%20konkurs&lang=pl&URL=http://www.regionwielkopolska.pl/konkurs/default/platnosc&URLC=http://www.regionwielkopolska.pl/konkurs/default/payment?sess=d3ceb0ff695ef001416a9624d9091c02&forename=Test&surname=test&email=test@test.pl&street=Testowa&street_n1=3&street_n2=1&city=Testowe&postcode=61-222&phone=91%20997%20997&country=testowo
    

    $template_dir=Bootstrap::$main->session('template_dir');
    
    Bootstrap::$main->tokens->set_wbp_js(
        array('fileUpload/js/vendor/jquery.ui.widget.js',
              'fileUpload/js/tmpl.min.js',
              'fileUpload/js/load-image.all.min.js',
              'fileUpload/js/canvas-to-blob.min.js',
              'fileUpload/js/jquery.blueimp-gallery.min.js',
              'fileUpload/js/jquery.iframe-transport.js',
              'fileUpload/js/jquery.fileupload.js',
              'fileUpload/js/jquery.fileupload-process.js',
              'fileUpload/js/jquery.fileupload-image.js',
              'fileUpload/js/jquery.fileupload-audio.js',
              'fileUpload/js/jquery.fileupload-video.js',
              'fileUpload/js/jquery.fileupload-validate.js',
              'fileUpload/js/jquery.fileupload-ui.js',
              
              'fileUpload/js/lang.js',
              'fileUpload/js/fun.js?t='.time()));
    

    $default_lang='pl';
    
    $include=$KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    $ajax=$include.'/ajax';
    $ajax_konkurs=$ajax.'/contest.php';
    $ajax_konkurs_action=$ajax.'/contest-action.php';

    $configuration_file_name=md5($sid);
    $configuration=WBP::get_data($configuration_file_name);
    
        
    $categories_options='';
    
    if (isset($configuration['drive']['cat']) && is_array($configuration['drive']['cat']) && count($configuration['drive']['cat']))
    {
        $categories_options='<option value="" class="choose_cat"></option>';
        foreach($configuration['drive']['cat'] AS $class)
        {
            $categories_options.='<option class="'.$class.'" value="'.WBP::str_to_url($contest_categories[$class]).'">'.$contest_categories[$class].'</option>';
        }
    }
    
    
    if (!isset($configuration['drive']['id']) || !$configuration['drive']['id'])
    {
        echo 'Brak arkusza';
        return;
    }
    
    //
?>

<link rel="stylesheet" href="<?php echo $template_dir?>/css/upload/style.css">
<link rel="stylesheet" href="<?php echo $template_dir?>/css/upload/blueimp-gallery.min.css">
<link rel="stylesheet" href="<?php echo $template_dir?>/css/upload/jquery.fileupload.css">
<link rel="stylesheet" href="<?php echo $template_dir?>/css/upload/jquery.fileupload-ui.css">




<div class="container-fluid">
  
    <form id="fileupload" action="post.php" method="POST" enctype="multipart/form-data">
              
        <input type="hidden" name="sid" value="<?php echo md5($sid);?>"/>
        <input type="hidden" name="id" value="" id="randid"/>
        <input type="hidden" name="ip" value="" id="fileuploadip"/>
    
    
        <div class="personal">
            <div style="display: none" class="lang_selector"><img id="lang_selector_img" src="<?php echo $IMAGES?>/lang-<?php echo $default_lang?>.gif"/></div>
                
            <h3><label for="basic"></label></h3>
            <div class="col-lg-7">                 
                    <div>
                        <label for="name"></label><input name="name" type="text" class="txbx name required" />
                    </div>
                    <div>
                        <label for="surname"></label><input name="surname" type="text" class="txbx surname required" />
                    </div>
                    <div>
                        <label for="address"></label><input name="address" type="text" class="txbx address required" />
                    </div>
                    <div>
                        <label for="number"></label><input name="number" type="text" class="txbx number required" />
                    </div>
                    
                    <div>
                        <label for="city"></label><input name="city" type="text" class="txbx city required" />
                    </div>                
                     
                    <div>
                        <label for="postal"></label><input name="postal" type="text" class="txbx postal required" />
                    </div> 
                    <div>
                        <label for="country"></label><input name="country" type="text" class="txbx country required" required="1"/>
                    </div> 
                    <div>
                        <label for="phone"></label><input name="phone" type="text" class="txbx phone required" required="1"/>
                    </div> 
                    <div>
                        <label for="email"></label><input name="email" type="text" class="txbx email required" required="1"/>
                    </div> 
     
                    
                    <br/>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <h3><label for="photos"></label></h3>
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>

        <ul class="terms" style="display: none">
            <li><input name="agreeauthor" type="checkbox" class="chxbx required" required="1" value="yes"/> <label for="agreeauthor"></label></li>
            <li><input name="agreeterms" type="checkbox" class="chxbx required" required="1" value="yes"/> <label for="agreeterms"></label></li>
            <li><input name="agreepublish" type="checkbox" class="chxbx required" required="1" value="yes"/> <label for="agreepublish"></label></li>
            <li><input name="agreemarketing" type="checkbox" class="chxbx" value="yes"/> <label for="agreemarketing"></label></li>
        </ul>

        <div class="warning none"><label for="error"></label></div>

        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span><label for="add"></label></span>
                    <input type="file" name="files[]" multiple>
                </span>

                <button type="submit" class="btn btn-primary start none" id="upload">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span><label for="save">Zapisz</label></span>
                </button>

                <button type="submit" class="btn btn-primary" id="noupload">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span><label for="save">Zapisz</label></span>
                </button>                
                
                <button type="reset" class="btn btn-warning cancel none">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Rezygnacja</span>
                </button>
                <button type="button" class="btn btn-danger delete none">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Usuń</span>
                </button>
                <input type="checkbox" class="toggle none">
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <div class="label label-danger img-errors" style="display: none"><label for="uploaderr">err</label></div>
            
            <button type="button" class="btn btn-primary img-errors" style="display: none">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span><label class="pay">pay</label></span>
            </button> 
            
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
            
        </div>




    </form>
</div>



<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>


<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}

    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
	    <div><input name="files[{%=file.name%}][title]" class="placeholder photo_title required"/></div>
            <div><label class="photoset" for="files[{%=file.name%}][set]"></label><input type="checkbox" class="photo_set" name="files[{%=file.name%}][set]" onchange="photo_set_checked(this)" value="1"></div>
            <div><input name="files[{%=file.name%}][setno]" class="placeholder setno required" style="display: none"/></div>
            <?php if ($categories_options): ?>
            <div><select name="files[{%=file.name%}][category]" class="category required"><?php echo $categories_options ?></select></div>
            <?php endif; ?>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <textarea class="placeholder description required" name="files[{%=file.name%}][description]"></textarea>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        
            {% if (!i) { %}
        
            <button class="btn btn-primary start none" disabled>
                <i class="glyphicon glyphicon-upload"></i>
                <span>Wgraj</span>
            </button>
            
            <button class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span><label for="discard">Odrzuć</label></span>
            </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete none" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle none">
            {% } else { %}
                <button class="btn btn-warning cancel none">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>


<div id="foto-contest-payment" style="display:none">
    
<h3><label for="payment"></label></h3>

<div class="col-md-4 col-sm-4 col-xs-4">
    <form method="post" action="https://ssl.dotpay.eu" id="form-dotpay">
        <input type="hidden" name="id" value="<?php echo $dotpay_id;?>" />
        <input type="hidden" name="amount" value="<?php echo $configuration['drive']['price_pln'];?>" />
        <input type="hidden" name="description" value="Opłata za: <?php echo $this->webtd['title'];?>" />
        <input type="hidden" name="lang" value="<?php echo $lang?>" />
        <input type="hidden" name="URL" value="<?php echo $next?>" />
        <input type="hidden" name="URLC" value="<?php echo $ajax;?>/dotpay.php" />
        <input type="hidden" name="forename" value="" />
        <input type="hidden" name="surname" value="" />
        <input type="hidden" name="email" value="" />
        <input type="hidden" name="street" value="" />
        <input type="hidden" name="street_n1" value="" />
        <input type="hidden" name="street_n2" value="" />
        <input type="hidden" name="city" value="" />
        <input type="hidden" name="postcode" value="" />
        <input type="hidden" name="phone" value="" />
        <input type="hidden" name="country" value="" />

        <button type="submit" class="btn btn-primary dotpay">
            <i class="glyphicon glyphicon-dotpay"></i>
            <span>Dotpay</span>
        </button>        
    </form>
</div>

<div class="col-md-4 col-sm-4 col-xs-4">    
    <form action="https://www.paypal.com/cgi-bin/webscr" method="POST" id="form-paypal">
        <input type="hidden" name="cmd" value="_xclick" />
        <input type="hidden" name="business" value="<?php echo $paypal_id;?>" />
        <input type="hidden" name="item_name" value="<?php echo $this->webtd['title'];?>" />
        <input type="hidden" name="amount" value="<?php echo $configuration['drive']['price_eur'];?>" />
        <input type="hidden" name="currency_code" value="EUR" />
        <input type="hidden" name="no_shipping" value="1" />
        <input type="hidden" name="no_note" value="1" />
        <input type="hidden" name="charset" value="UTF-8" />
        <input type="hidden" name="custom" value="" />
        <input type="hidden" name="return" value="<?php echo $next?>" />
        <input type="hidden" name="cancel_return" value="<?php echo $next?>?cancel" />
        <input type="hidden" name="notify_url" value="<?php echo $ajax;?>/paypal.php" />
        
        <button type="submit" class="btn btn-primary paypal">
            <i class="glyphicon glyphicon-paypal"></i>
            <span>Paypal</span>
        </button> 
    </form>
</div>

<div class="col-md-4 col-sm-4 col-xs-4">
    <button type="button" class="btn btn-primary traditional" onclick="$('.traditional').fadeIn()">
            <i class="glyphicon glyphicon-traditional"></i>
            <span>Przelew tradycyjny</span>
    </button>    
</div>

<div class="clearfix"></div>

    <div class="traditional" style="display: none">
        <h4><label for="please_pay">Prosimy o wpłatę na konto</label>:</h4>
        <p><label for="amount">Kwota</label>: <?php echo $configuration['drive']['price_pln'];?> zł<br/>
        WBPiCAK <br/>
        60-819 Poznań, ul. Prusa 3<br/>
        IBAN: <strong>PL 89 1500 1621 1216 2000 8820 0000</strong> (<label for="payment_title">tytuł wpłaty</label>: <?php echo $this->webtd['title'];?>), SWIFT: WBKPPRPP</p>
    </div>
    
</div>



<script type="text/javascript">
    
var min_images = <?php echo $configuration['drive']['img_min']+0?>;
var max_images = <?php echo $configuration['drive']['img_max']+0?>;
   
window.onload = function() {
    
    
    switch_wbp_lang('<?php echo $lang?>');
    
    contest_form_action='<?php echo $ajax_konkurs_action;?>';
    var max = max_images>0 ? max_images : null;
    $('#fileupload').fileupload({
        url: '<?php echo $ajax_konkurs?>',
        sequentialUploads: true,
        autoUpload: false,
        maxNumberOfFiles: max,
        maxChunkSize: 500000, // 0.5M
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|tiff?)$/i,
        maxFileSize: 31200000,  // 30 MB
        minFileSize: 100000,     // 100K
        maxRetries: 50,
        retryTimeout: 500
    });

    foto_init_validation();
    
    $('#lang_selector_img').click(switch_wbp_lang);
    



    <?php if (isset($KAMELEON_MODE) && $KAMELEON_MODE==1):?>
    $('#fileupload').dblclick(fill_the_form);
    <?php endif ?>

}    
</script>
