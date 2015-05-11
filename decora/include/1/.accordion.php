<?php

$weblink = new weblinkModel();
$webtd = new webtdModel($sid);

$webpage = Bootstrap::$main->tokens->webpage;

$subpath = 'accordion';


if ($webtd->menu_id) {
    $links = $weblink->getAll($webtd->menu_id, 2);

    $options = '';

    foreach ($links AS $link) {
        if (!$link['img']) continue;
        $imga = $subpath . '/' . $link['img'];
        $path = $session['uimages_path'] . '/' . $imga;
        if (!$link['imga'] || $imga != !$link['imga']) {
            $weblink->load($link);
            $weblink->imga = $imga;
            $weblink->save();

        }

        $selected = $link['sid'] == $webpage['pagekey'] ? 'selected' : '';
        $options .= '<option ' . $selected . ' value="' . $link['sid'] . '">' . $link['img'] . '</option>';

        $dir = dirname($path);
        if (!file_exists($dir)) mkdir($dir, 0755, true);
	
	
        if ( !file_exists($path) || filemtime($session['uimages_path'] . '/' . $link['img']) > filemtime($path)) {
            Bootstrap::$main->kameleon->min_image($session['uimages_path'] . '/' . $link['img'], $path, $width?:97, $size?:97, false, true);
        }
    }
}

$ajax_path = $session['uincludes_ajax'] . '/.accordion-backend.php';



$menus=array();

if ($cos) {
    $weblink=new weblinkModel();
    
    foreach ($weblink->getAll(0,PAGE_MODE_EDIT,$cos) AS $link)
    {
        if ($link['alt']) $menus[]=$link['alt'];
    }    
}

?>

    <style type="text/css">

        .hotspot {
            cursor  : move;
            display : block;
        }

        .hotspot_menu li {
            background : #f1f1f1;
            padding    : 5px;
            cursor     : pointer;
        }
	
	.km_tdin .carousel-inner > .item.active {
	    overflow: hidden;
	}
	
    </style>

    <select id="photoSelector-<?php echo $sid ?>">
        <option value='0'>Wybierz pierwsze zdjęcie</option>
        <?php echo $options ?>
    </select>
    
    <div id="inspirationMenuChoose-<?php echo $sid ?>" style="display: none">
        <ul>
        <?php foreach ($menus AS $m): ?>
        <li><input type="checkbox" value="<?php echo $m?>" /><?php echo $m?></li>
        <?php endforeach ?>
        </ul>
    </div>


    <script type="text/javascript">
    
    
    	<?php if(count($menus)): ?>
    		$(function(){
    			
    			var openedCheckboxes = false;
    			var currentOpenedSID;
    			
    			//move div
    			$('#accordion-thumbs-wrapper-<?php echo $sid ?>').append('#inspirationMenuChoose-<?php echo $sid ?>');
    			
    			$('#accordion-thumbs-<?php echo $sid ?> img').click(function(){
    				var thisImage = $(this);
    				var k = $('#inspirationMenuChoose-<?php echo $sid ?>');
    				var sid = $(this).attr('sid');
    				
    				
    				//k.toggle();
    				if(openedCheckboxes)
    				{
    					if(currentOpenedSID == sid)
    					{
    						//close this
    						//console.log("Is opened");
	    					k.css('display', 'none');
	    					openedCheckboxes = false;
	    					return;
    					}
    					else
    					{
    						//dont close change data
    						currentOpenedSID = sid;
    					}
    					
    				}
    				else
    				{
   						//console.log("is closed");
   						k.css('display', 'block');
   						openedCheckboxes = true;
 						currentOpenedSID = sid;
    				}
    				
    				
    				k.find('input[type=checkbox]').attr('checked', false);
    				k.find('input[type=checkbox]').off('click');
    				//k.css('display', 'block');
    				
    				
    				
    				var previousClicked = $(this).attr('data');
    				var clickedArray = previousClicked.split(",");
    				
    				//console.log("Data: " + clickedArray);
    				$.each(clickedArray, function(key, value){
    					//console.log("k:" + key + " v:" + value);
    					//k.find('input[type=checkbox][value="'+ value + '"]').attr('checked', false);
    					//k.find('input[type=checkbox][value="'+ value + '"]').checkboxradio('refresh');
    					var a = k.find('input[type=checkbox][value="'+ value + '"]').prop('checked', true);
    					//console.log("p ");
    					//console.log(a);
    				});
    				
    				//k.find('input[type=checkbox]').attr('checked', false).click(function(){
					k.find('input[type=checkbox]').on('click', function(){
						var clicked ="";
						var checkedControls = k.find('input:checked');
						//console.log(checkedControls);
						checkedControls.each(function(){
							//console.log(this.value);
							clicked += ',' + this.value;
						})
						/*
						k.find('input[type=checkbox]').each(function(){
							if(this.checked)
							{
								clicked += ',' + this.value; //attr('value');
							}
						});
						*/
						$.ajax('<?php echo $ajax_path;?>?sid=' + sid + '&inspiration_type=' + clicked).done(function(data){
							console.log("Ajax : " + data);
						});
						
						//save to this image
						thisImage.attr('data', clicked);
						console.log(clicked);
						console.log(sid);
					})
					
    			});
    			
    		});
    	
    	<?php endif; ?>
        
        jQueryKam(function ($) {
            $("#hotspot-menu-add-<?php echo $sid ?>, #hotspot-menu-edit-<?php echo $sid ?>").detach().appendTo("body");

            $("#photoSelector-<?php echo $sid ?>").detach().insertAfter("#accordion-wrapper-<?php echo $sid ?>").on("change", function () {
                $.getJSON("<?php echo $ajax_path ?>", {
                    photo: this.value,
                    page: "<?php echo $webpage['sid']?>"
                }, function (data) {

                });
            });

            $('#accordion-wrapper-<?php echo $sid ?>').on("mouseenter", ".hotspot", function (e) {
                //$("#accordion-carousel-<?php echo $sid ?> .hotspot")
		$(this).contextmenu("#hotspot-menu-edit-<?php echo $sid ?>", function (target) {
                    $("#hotspot-remove-<?php echo $sid ?>").off().on("click", function (e) {
                        $.getJSON("<?php echo $ajax_path ?>", {
                            remove: target.attr("sid")
                        }, function (data) {
                            target.remove();
                        });
                    });
                    $("#hotspot-edit-<?php echo $sid ?>").off().on("click", function (e) {
                        document.location = KAM_ROOT + "menu/edit_link/" + target.attr("sid");
                    });
                });
            });

            var dragOpt = {
                start: function (e, ui) {

                },
                stop: function (e, ui) {
                    var item = $("#accordion-wrapper-<?php echo $sid ?> .item.active");

                    var left = ui.position.left * 100 / item.width();
                    var top = ui.position.top * 100 / item.height();

                    $.getJSON("<?php echo $ajax_path ?>", {
                        move: $(this).attr("sid"),
                        coordinates: left + "x" + top
                    });
                }
            };

            $("#accordion-wrapper-<?php echo $sid ?> .item").contextmenu("#hotspot-menu-add-<?php echo $sid ?>", function (target) {
                $("#hotspot-add-<?php echo $sid ?>").off().on("click", function (e) {
                    var item = $("#accordion-wrapper-<?php echo $sid ?> .item.active");

                    var x = e.pageX - item.offset().left;
                    var y = e.pageY - item.offset().top;

                    var left = x * 100 / item.width();
                    var top = y * 100 / item.height();

                    $.getJSON("<?php echo $ajax_path ?>", {
                        add: item.attr("sid"),
                        coordinates: left + "x" + top
                    }, function (data) {
                        $("<div></div>").addClass("hotspot").attr("sid", data.sid).css("top", top + "%").css("left", left + "%").appendTo(item).draggable(dragOpt);
                    })
                });
            });

            $("#accordion-wrapper-<?php echo $sid ?> .hotspot").draggable(dragOpt);
        });
    </script>
    <ul id="hotspot-menu-add-<?php echo $sid ?>" class="km_jqcontextmenu hotspot_menu">
        <li id="hotspot-add-<?php echo $sid ?>">DODAJ HOTSPOT</li>
    </ul>

    <ul id="hotspot-menu-edit-<?php echo $sid ?>" class="km_jqcontextmenu hotspot_menu">
        <li id="hotspot-remove-<?php echo $sid ?>">USUŃ HOTSPOT</li>
        <li id="hotspot-edit-<?php echo $sid ?>">PRZEJDŹ DO EDYCJI</li>
    </ul>

<?php
include __DIR__ . '/accordion.php';
