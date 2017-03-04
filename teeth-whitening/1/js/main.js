
$(document).ready(function () {
    setTimeout(function(){
        $(document).trigger('scroll');
        
        $('.info-block .img-wr img,.info-block_mod .img-wr img').each(function(){
		if ($(this).height()<400) return;
            $(this).closest('div.info-block,div.info-block_mod').css({
                'padding-bottom':0,
                'height':$(this).height()+'px'
            });
    
        });
        
    },50);
    
})
