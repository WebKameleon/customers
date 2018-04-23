
var image_files_counters = {};

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
        if (!$('#fileupload .terms').hasClass('forgetit')) $('#fileupload .terms').fadeIn();
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

var uploaded_images=0,failed_images=0, total_images=0;

function upload_started(e,data)
{
    if (typeof(image_files_counters[data.files[0].name])=='undefined' || image_files_counters[data.files[0].name]>0) return;
    image_files_counters[data.files[0].name] |=1;
        
    if (0==uploaded_images++) {
        failed_images=0;
        $('#wbp-form-loading').height($(document).height()).fadeIn();
    }
    total_images++;
    
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
        if (next.substr(0,4)!='http') $('#form-paypal input[name=return]').val(deUpDir(dirname(location.href)+next));
    }
    
    next = $('#form-paypal input[name=cancel_return]').val();
    if (next.substr(0,1)=='/') {
        $('#form-paypal input[name=cancel_return]').val(location.origin)+next;
    } else {
        if (next.substr(0,4)!='http') $('#form-paypal input[name=cancel_return]').val(deUpDir(dirname(location.href)+next));
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

function check_failed() {
    //console.log('check_failed',uploaded_images,failed_images);
    
    if (uploaded_images-failed_images==0) {
        $('#wbp-form-loading').fadeOut();
        var all_failed = failed_images==total_images;
        uploaded_images=0;
        failed_images=0;
        total_images=0;
        
        $('.personal').fadeOut();
        setTimeout(function(){
            $('.img-errors').fadeIn(500,function(){
                if (all_failed) {
                    $('button.img-errors').hide();
                }
                
            });
            $('#fileupload .terms').fadeOut();
            $('#fileupload .terms').addClass('forgetit');
            $('table.table p.name').each(function(){
                var tr=$(this).closest('tr');
                var err=tr.find('span.label');
                if (err.length==0) {
                    tr.fadeOut();
                    tr.addClass('show-afterwords');
                }
            });        
        
        },1000);
        

    }   
}

var last_upload_done_data;

function upload_done(e,data)
{    
    if (data==null) data=last_upload_done_data;
    else last_upload_done_data=data;
    
    
    if (--uploaded_images==0) {
        $('#wbp-form-loading').fadeOut();
        $('#fileupload .personal,#fileupload .terms,.fileupload-buttonbar').fadeOut(1000);
        
        rewrite_data_to_payment();
        var file=data._response.result.files[0];
        //$('#form-dotpay input[name=URL]').val(dirname(location.href)+data.url.replace('contest.php','dotpay.php')+'?id='+file.id);
        //$('#form-paypal input[name=notify_url]').val(dirname(location.href)+data.url.replace('contest.php','paypal.php'));

        if ($('#form-dotpay input[name=URLC]').val().indexOf('http:')<0) 
            $('#form-dotpay input[name=URLC]').val(deUpDir(dirname(location.href)+$('#form-dotpay input[name=URLC]').val()+'?id='+file.id));
        if ($('#form-paypal input[name=notify_url]').val().indexOf('http:')<0) {
        	$('#form-paypal input[name=notify_url]').val(deUpDir(dirname(location.href)+$('#form-paypal input[name=notify_url]').val()));
        }

        $('#form-paypal input[name=custom]').val(file.id);
        $('#foto-contest-payment').fadeIn(500);
        
        var url=file.done+'?id='+file.id;
        $.get(url);
        //console.log(data._response.result.files[0]);
        //console.log(data);
        
        $('.template-download .label-danger').closest('tr').hide();
        $('.show-afterwords').show();
    } else check_failed();
    
}

$('button.img-errors').click(function(e){
    uploaded_images=1;
    upload_done(e);
});


var contest_form_action='';
var contest_form_url='';


function foto_init_url(cb) {
    $.getJSON(contest_form_action,function(data) {
        contest_form_url=data.url;
        $('#fileuploadip').val(data.ip);
        if (typeof(cb)=='function') cb(data);
    });
}


function foto_init_form(photo) {
    var contest_form_data=contest_form_action.replace('contest-action','contest-data');
    
    $.getJSON(contest_form_data,function(data) {
        
        if (typeof(data.data)=='undefined') return;
        
        if (photo==null) {
            for (var k in data.data) {
                $('#fileupload input[type="text"][name="'+k+'"]').val(data.data[k]);
            }
        } else {
            
            for (var i=0; i<photo.files.length; i++) {
                var name=photo.files[i].name;
               
                if (typeof(data.files[name])!='undefined') {
                    
                    var inputname='files['+name+'][title]';
                    $('#fileupload input[name="'+inputname+'"]').val(data.files[name].title);
                    inputname='files['+name+'][description]';
                    $('#fileupload textarea[name="'+inputname+'"]').val(data.files[name].description);
                   
                    if (typeof(data.files[name].category)!='undefned') {
                        var selectname='files['+name+'][category]';
                        $('#fileupload select[name="'+selectname+'"]').val(data.files[name].category);
                    }
                   
                    if (typeof(data.files[name].setno)!='undefined' && data.files[name].setno.length>0) {
                        inputname='files['+name+'][set]';
                        $('#fileupload input[name="'+inputname+'"]').prop('checked',true);
                        
                        photo_set_checked($('#fileupload input[name="'+inputname+'"]')[0]);
                        
                        inputname='files['+name+'][setno]';
                        $('#fileupload input[name="'+inputname+'"]').val(data.files[name].setno);
                    }
                   
                }
            }
        }
     
    });
    count_files();
}


function foto_init_validation()
{
    foto_init_form();
    
    $('#fileupload')
        .bind('fileuploadsend', function (e, data) {
            if (typeof(image_files_counters[data.files[0].name])=='undefined' || image_files_counters[data.files[0].name]>1) return;
            image_files_counters[data.files[0].name] |=2;
            
            data.url=contest_form_url;
            foto_init_url();
        }).bind('fileuploadadd', function (e, data) {
            image_files_counters[data.files[0].name] = 0;
            setTimeout(foto_init_form,700,data);
            setTimeout(count_files,1300);
            if (contest_form_url.length==0) foto_init_url();
            
            $('.fileinput-button').removeClass('error');
        }).bind('fileuploadfail', function (e, data) {
            
            var fu = $(this).data('blueimp-fileupload') || $(this).data('fileupload');
            console.log('fail',data.files[0].name,image_files_counters[data.files[0].name],fu.options.maxRetries);
            
            if (typeof(data.errorThrown)=='string' && data.errorThrown=='abort') {
                return $.blueimp.fileupload.prototype.options.fail.call(this, e, data);
            }
            
            if (image_files_counters[data.files[0].name]++ > fu.options.maxRetries) {
                failed_images++;
                check_failed();
                setTimeout(count_files,300);                
            } else {
                foto_init_url(function(d){
                    setTimeout(function(){
                        data.data = null;
                        data.url=d.url;
                        data.submit();
                        console.log('resubmit',data.files[0]);
                    },fu.options.retryTimeout);                                
                });

            }
            
            
            $.blueimp.fileupload.prototype.options.fail.call(this, e, data);
            
        }).bind('fileuploaddone', upload_done)
        .bind('fileuploadsubmit', upload_started)
        .bind('fileuploadstart', function (e) {
            $('.img-errors').fadeOut();
        });
    
    
    
    $('#fileupload').find('input.required,select.required,textarea.required').on("change",count_files);

    $('#noupload').click(cant_upload);
    $('#randid').val(Math.random());
}


var chunkFailed = function(e,data) {
    
}
