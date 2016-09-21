function count_errors(add_class,verbose)
{
    var errors=0;
    $('#fileupload').find('input.required,select.required,textarea.required').each(function() {
    
        if ($(this).css('display')!='none')
        {
            var tn=$(this).prop('tagName');
            var type=$(this).prop('type');

 
            switch (type.toLowerCase()) {
                case 'checkbox':

                    if (!$(this).prop('checked')) {
                        errors++;
                        if (add_class) $(this).addClass('error');
                        if (verbose) console.log(this);
                    }
                    break;
                default:
                    if ($(this).val().trim().length==0)
                    {
                        errors++;
                        if (add_class)  $(this).addClass('error');
                        if (verbose) console.log(this);
                    }
                    break;
            }
        }
    });
    
    
    var f=$("#fileupload .template-upload:not('.ui-state-error')").length;
    
    
    if (f<min_images)
    {
        errors++;
        
        if (add_class) {
            
            $('.fileinput-button').addClass('error').attr('title','minNumberOfFiles');
            $('.warning').attr('title','minNumberOfFiles');
            
        }
        if (verbose) console.log('too little images');
        $('#fileupload .terms').hide();
    }
    else
    {
        $('#fileupload .terms').fadeIn();
    }

    if (max_images>0 && f>max_images)
    {
        errors++;
        
        if (add_class) {
            
            $('.fileinput-button').addClass('error').attr('title','maxNumberOfFiles');
            $('.warning').attr('title','maxNumberOfFiles');
            
        }
        if (verbose) console.log('too many images');
    }
    
    return errors;
}



function count_files(obj)
{
  
    setTimeout(apply_wbp_lang,300);    
    $('#fileupload').find('.warning').fadeOut();
    
    
    if (typeof(obj) == 'object') {
        $(obj.target).removeClass('error');
    }
    
    if (typeof(obj) == 'undefined') {
        $('#fileupload .template-upload').find('input.required,select.required,textarea.required').on("change",count_files);
    }

    
    var errors=count_errors(false,false);
    
    if (errors==0) {
        $("#upload").fadeIn();
        $("#noupload").fadeOut();
        
    } else {
        $("#noupload").fadeIn();
        $("#upload").fadeOut();
    }

    
    
}


function cant_upload()
{
    var errors=count_errors(true,true);
    apply_wbp_lang();
    
    if (errors>0) {
        $('#fileupload').find('.warning').fadeIn();
    }
    
    return false;
}

function photo_set_checked (chbx)
{
    var title=chbx.name.replace('[set]','[title]');
    var setno=chbx.name.replace('[set]','[setno]');
    
    if (chbx.checked)
    {
        $("input[name='"+title+"']").removeClass('photo_title').addClass('set_title');
        $("input[name='"+setno+"']").fadeIn();
        
    }
    else
    {
        $("input[name='"+title+"']").removeClass('set_title').addClass('photo_title');
        $("input[name='"+setno+"']").fadeOut();
        
    }
    apply_wbp_lang();
}

function fill_the_form() {
    $('select.required').val('Wydarzenia').removeClass('error');
    $('input.required').val('test').removeClass('error');
    $('textarea.required').val('test').removeClass('error');
    $('input.required[type="checkbox"]').prop('checked',true).removeClass('error');
    
    rewrite_data_to_payment();
    count_files();
}

var uploaded_images=0;

function upload_started(e,data)
{
    if (0==uploaded_images++) {
        $('#wbp-form-loading').height($(document).height()).fadeIn();
    }
}

function dirname(path) {
    if (path.substr(-1)=='/') return path;
    var ret=path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '');
    if (ret.substr(-1)!='/') ret+='/';
    return ret;
}


function rewrite_data_to_payment()
{
    
    $('#form-dotpay input[name=lang]').val(wbp_photo_lang);
    $('#form-dotpay input[name=forename]').val( $('#fileupload input[name=name]').val() );
    $('#form-dotpay input[name=surname]').val( $('#fileupload input[name=surname]').val() );
    $('#form-dotpay input[name=email]').val( $('#fileupload input[name=email]').val() );
    $('#form-dotpay input[name=street]').val( $('#fileupload input[name=address]').val() );
    $('#form-dotpay input[name=street_n1]').val( $('#fileupload input[name=number]').val() );
    $('#form-dotpay input[name=city]').val( $('#fileupload input[name=city]').val() );
    $('#form-dotpay input[name=postcode]').val( $('#fileupload input[name=postal]').val() );
    $('#form-dotpay input[name=phone]').val( $('#fileupload input[name=phone]').val() );
    $('#form-dotpay input[name=country]').val( $('#fileupload input[name=country]').val() );
    
    var next = $('#form-dotpay input[name=URL]').val();
    if (next.substr(0,1)=='/') {
        $('#form-dotpay input[name=URL]').val(location.origin)+next;
    } else {
        if (next.substr(0,4)!='http') $('#form-dotpay input[name=URL]').val(dirname(location.href)+next);
    }
    next = $('#form-dotpay input[name=URL]').val();
    while (next.match(/\/\.\.\//)) next=next.replace(/[^\/]+\/\.\.\//,'');
    $('#form-dotpay input[name=URL]').val(next);
    
    next = $('#form-paypal input[name=return]').val();
    if (next.substr(0,1)=='/') {
        $('#form-paypal input[name=return]').val(location.origin)+next;
    } else {
        if (next.substr(0,4)!='http') $('#form-paypal input[name=return]').val(dirname(location.href)+next);
    }
    
    next = $('#form-paypal input[name=cancel_return]').val();
    if (next.substr(0,1)=='/') {
        $('#form-paypal input[name=cancel_return]').val(location.origin)+next;
    } else {
        if (next.substr(0,4)!='http') $('#form-paypal input[name=cancel_return]').val(dirname(location.href)+next);
    }    
}

function deUpDir(urlc) {
    
    var len=urlc.length;
    
    while (true) {
        urlc=urlc.replace(/\/[^\/]+\/\.\.\//,'/');
        if (len==urlc.length) break;
        len=urlc.length;
    }
    
    return urlc;
}

function upload_done(e,data)
{
    
    if (--uploaded_images==0) {
        $('#wbp-form-loading').fadeOut();
        $('#fileupload .personal,#fileupload .terms,.fileupload-buttonbar').fadeOut(1000);
        
        rewrite_data_to_payment();
        var file=data._response.result.files[0];
        //$('#form-dotpay input[name=URL]').val(dirname(location.href)+data.url.replace('contest.php','dotpay.php')+'?id='+file.id);
        //$('#form-paypal input[name=notify_url]').val(dirname(location.href)+data.url.replace('contest.php','paypal.php'));

        if ($('#form-dotpay input[name=URLC]').val().indexOf('http:')<0) 
            $('#form-dotpay input[name=URLC]').val(deUpDir(dirname(location.href)+$('#form-dotpay input[name=URLC]').val()+'?id='+file.id));
        if ($('#form-paypal input[name=notify_url]').val().indexOf('http:')<0) 
        	$('#form-paypal input[name=notify_url]').val(deUpDir(dirname(location.href)+$('#form-paypal input[name=notify_url]').val()));

        $('#form-paypal input[name=custom]').val(file.id);
        $('#foto-contest-payment').fadeIn(500);
        
        var url=file.done+'?id='+file.id;
        $.get(url);
        //console.log(data._response.result.files[0]);
        //console.log(data);
    }
    
}


var contest_form_action='';
var contest_form_url='';


function foto_init_validation()
{
    
    $('#fileupload')
        .bind('fileuploadsend', function (e, data) {
            data.url=contest_form_url;
        }).bind('fileuploadadd', function (e, data) {
            setTimeout(count_files,300);
            if (contest_form_url.length==0) {
                $.getJSON(contest_form_action,function(data) {
                    contest_form_url=data.url;
                    $('#fileuploadip').val(data.ip);
                });
            }

            $('.fileinput-button').removeClass('error');
        }).bind('fileuploadfail', function (e, data) {
            setTimeout(count_files,300);
        }).bind('fileuploaddone', upload_done).bind('fileuploadsubmit', upload_started);
        
        
    
    $('#fileupload').find('input.required,select.required,textarea.required').on("change",count_files);

    $('#noupload').click(cant_upload);
    $('#randid').val(Math.random());
}
