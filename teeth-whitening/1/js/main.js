
$(document).ready(function () {
    setTimeout(function(){
        $(document).trigger('scroll');
        
        $('.info-block .img-wr img,.info-block_mod .img-wr img').each(function(){
            $(this).closest('div.info-block').css({
                'padding-bottom':0,
                'height':$(this).height()+'px'
            });
    
        });
        
    },50);
    
})
