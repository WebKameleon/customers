
var image_files_counters = {};
var uploaded_images=0,failed_images=0, total_images=0;
var last_upload_done_data;
var client_id;

function count_errors(add_class,verbose,check_personal,check_images,check_terms)
{
    var errors=0;
    var selectors = [];
    if (check_personal) 
        selectors.push('#fileupload .personal input.required');
    if (check_images)
        selectors.push('#fileupload .files input.required,#fileupload .files select.required,#fileupload .files textarea.required');
    if (check_terms) {
        selectors.push('#fileupload .terms input.required');
    }
    
    $(selectors.join(',')).each(function() {
        
    
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
    
    if (check_images) {
    
        var f=$("#fileupload .template-download:not('.ui-state-error')").length;
        
        
        if (f<min_images)
        {
            errors++;
            
            if (add_class) {
                
                $('.fileinput-button').addClass('error').attr('title','minNumberOfFiles');
                $('.warning').attr('title','minNumberOfFiles');
                
            }
            if (verbose) console.log('too little images');
            //$('#fileupload .terms').hide();
        }
        else
        {
            if (!$('#fileupload .terms').hasClass('forgetit') && check_terms) $('#fileupload .terms').fadeIn();
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
        $('#fileupload .template-download').find('input.required,select.required,textarea.required').on("change",count_files);
    }

    
    var errors=count_errors(false,false,true,false,true);
    
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
    var errors=count_errors(true,true,true,true,true);
    apply_wbp_lang();
    
    if (errors>0) {
        $('#fileupload .warning').fadeIn();
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
    $('input.required').removeClass('error').each(function(){
        if ($(this).val().length==0) $(this).val('Test');
    });
    $('textarea.required').val('test').removeClass('error');
    $('input.required[type="checkbox"]').prop('checked',true).removeClass('error');
    
    rewrite_data_to_payment();
    count_files();
}



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
            //$('#fileupload .terms').fadeOut();
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



function client_finished() {
   
    
    $('#wbp-form-loading').fadeOut();

    $('#fileupload .personal,#fileupload .terms,.fileupload-buttonbar').fadeOut(1000);
        
    rewrite_data_to_payment();
 
    if (typeof(file)!='undefined') {
        if ($('#form-dotpay input[name=URLC]').val().indexOf('http:')<0) 
            $('#form-dotpay input[name=URLC]').val(deUpDir(dirname(location.href)+$('#form-dotpay input[name=URLC]').val()+'?id='+file.id));
        if ($('#form-paypal input[name=notify_url]').val().indexOf('http:')<0) {
            $('#form-paypal input[name=notify_url]').val(deUpDir(dirname(location.href)+$('#form-paypal input[name=notify_url]').val()));
        }
        $('#form-paypal input[name=custom]').val(client_id);
        
    } else {
        if ($('#form-dotpay input[name=URLC]').val().indexOf('http:')<0) 
            $('#form-dotpay input[name=URLC]').val(deUpDir(dirname(location.href)+$('#form-dotpay input[name=URLC]').val()+'?id='));
        if ($('#form-paypal input[name=notify_url]').val().indexOf('http:')<0) {
            $('#form-paypal input[name=notify_url]').val(deUpDir(dirname(location.href)+$('#form-paypal input[name=notify_url]').val()));
        }            
    }
    
    $('#foto-contest-payment').fadeIn(500);

    $('.template-download .label-danger').closest('tr').hide();
    $('.show-afterwords').show();
    
    $('#fileupload table button.btn').hide();
    $('#fileupload table .files input,#fileupload table .files textarea,#fileupload table .files select').each(function(){
        
        var type=$(this).attr('type');
        var val;
        if (type!=undefined && type=='checkbox') {
            val=$(this).prop('checked')?' &#10004;':' -';
        } else {
            if ($(this).prop('tagName')=='SELECT') 
                val='<p>'+$(this).find('option[value="'+$(this).val()+'"]').text()+'</p>';
            else
                val='<p>'+$(this).val()+'</p>';
        }
        if (type=='hidden') return;
        
        $(this).replaceWith(val);
        
    });

}




function upload_done(e,data)
{    
    if (data==null) data=last_upload_done_data;
    else last_upload_done_data=data;
    
    $('#fileupload .personal').hide();
    
    setTimeout(apply_wbp_lang,500);
    
    $('#saveall').fadeIn(500);
    
    if (--uploaded_images==0) {
        $('#wbp-form-loading').fadeOut();
        
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
    }).fail(function(){
        if (typeof(cb)=='function') cb();
    });
}


function foto_init_fotm_photo(data) {
    
    $('#saveall').fadeIn(500);
    
    for (var name in data.files) {
 
        var inputname='files['+name+'][title]';
        $('#fileupload input[name="'+inputname+'"]').val(data.files[name].title);
        inputname='files['+name+'][description]';
        $('#fileupload textarea[name="'+inputname+'"]').val(data.files[name].description);
       
        if (typeof(data.files[name].category)!='undefned') {
            var selectname='files['+name+'][category]';
            $('#fileupload select[name="'+selectname+'"]').val(data.files[name].category);
        }
       
        if (typeof(data.files[name].setno)!='undefined' && data.files[name].setno.length>0 && typeof(data.files[name].set)!='undefined' && data.files[name].set==1) {
            inputname='files['+name+'][set]';
            $('#fileupload input[name="'+inputname+'"]').prop('checked',true);
            
            photo_set_checked($('#fileupload input[name="'+inputname+'"]')[0]);
            
            inputname='files['+name+'][setno]';
            $('#fileupload input[name="'+inputname+'"]').val(data.files[name].setno);
        }
       
    }
    
}

function foto_init_form(photo) {
    var contest_form_data=contest_form_action.replace('contest-action3','contest-data3');
    
    $.getJSON(contest_form_data,function(data) {
        
        client_id = data.id;
        $('#clientid').val(data.id);
        
        if (photo) return;
        
        if (data.data) for(k in data.data){
            if (k=='files') {
                setTimeout(foto_init_fotm_photo,1000,data.data);
            } else {
                $('#fileupload input[type="text"][name="'+k+'"]').val(data.data[k]);
            }
        }
    
        
        if (data.files) {
            var files=[];
            for (k in data.files) {
                files.push(data.files[k]);
            }
            
            if (files.length) {
                $('#fileupload').fileupload('option', 'done')
                    .call($('#fileupload'), $.Event('fileuploaddone'), {result:{files: files}});
            }
            
            last_upload_done_data = {_response:{result:{files:files}}};
            
            console.log(last_upload_done_data)
        }
        
        return;
        
     
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
            $('#fileupload .terms').fadeIn(1000);
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
                var resubmit = function(url) {
                    data.data = null;
                    data.url=url;
                    data.submit();
                    console.log('resubmit',data.files[0].name);
                }
                var getNewUrl = function(){
                    
                    foto_init_url(function(d){
                        if (d) setTimeout(resubmit,fu.options.retryTimeout,d.url);
                        else setTimeout(getNewUrl,fu.options.retryTimeout);
                    });
                    
                }
                
                getNewUrl();
            }
            
            
            $.blueimp.fileupload.prototype.options.fail.call(this, e, data);
            
        }).bind('fileuploaddone', upload_done)
        .bind('fileuploadsubmit', upload_started)
        .bind('fileuploadstart', function (e) {
            $('.img-errors').fadeOut();
        });
    
    
    
    $('#fileupload').find('input.required,select.required,textarea.required').on("change",count_files);

    $('#noupload').click(cant_upload);
    
    
    $('.fileinput-button input').click(function(){
        if (count_errors(true,false,true,false,false)>0) {
            $('#fileupload .warning').fadeIn();
            return false;
        }
        return true;
    });
    
    $('#fileupload button.discard-all').click(function(){
        if (confirm($(this).attr('ql'))) {
            var url=contest_form_action.replace('contest-action3','contest-clear');
            console.log('u',url);
            $.get(url,function(ok){
                if (ok=='OK') {
                    location.reload();
                }
            });
        }
    });
    
    $('#fileupload button.save-all').click(function(){
        
        if (count_errors(true,true,true,true,false)>0) {
            $('#fileupload .warning').fadeIn();
            return false;
        }
        $('#wbp-form-loading').show();
        $.post($('#fileupload').attr('action'),$('#fileupload').serialize(),function(result){
            
            if (result=='OK') {
                client_finished();
            } else {
                $('#wbp-form-loading').hide();
            }
        });
        return true;
    });
    
    $(document).on('change','#fileupload .required',function(){
        $(this).removeClass('error');
        $('#fileupload .warning').hide();
    });
}


var chunkFailed = function(e,data) {
    
}
