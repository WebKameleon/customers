"use strict";
function addError(messages, error, input) {
    var block = document.createElement("p");
    block.classList.add("text-danger");
    block.classList.add("error");
    block.innerText = error;
    messages.appendChild(block);
    $(input).addClass("input-danger");
      }
function showErrorsForInput(input, error) {
    // This is the root of the input

    var formGroup = closestParent(input.parentNode, "form-group")
        // Find where the error messages will be insert into
        ,
        messages = formGroup.querySelector(".messages");
    // First we remove any old messages and resets the classes
    resetFormGroup(formGroup);
    // If we have errors
    if (error) {
        // we first mark the group has having errors
        formGroup.classList.add("has-error");
        addError(messages, error, input);
        
    } else {
        // otherwise we simply mark it as success
        formGroup.classList.add("has-success");
    }
}

// Recusively finds the closest parent that has the specified class
function closestParent(child, className) {
    if (!child || child == document) {
        return null;
    }
    if (child.classList.contains(className)) {
        return child;
    } else {
        return closestParent(child.parentNode, className);
    }
}

function resetFormGroup(formGroup) {

    // Remove the success and error classes
    formGroup.classList.remove("has-error");
    formGroup.classList.remove("has-success");
    // and remove any old messages
    _.each(formGroup.querySelectorAll(".text-danger"), function(el) {
        el.parentNode.removeChild(el);
    });
}

function notify(message, type){
    $.growl({
        message: message
    },{
        type: type,
        allow_dismiss: true,
        label: 'Cancel',
        className: 'btn-xs waves-effect',
        placement: {
            from: 'top',
            align: 'right'
        },
        delay: 5000,
        animate: {
                enter: 'animated fadeInLeft',
                exit: 'animated fadeOutLeft'
        },
        offset: {
            x: 30,
            y: 30
        }
    });
};

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};


function processing(on) {
    if (on) {

        $('body').append('<div id="processing" style="position:absolute; top:0; left:0; height:100%; width:100%; z-index: 99999999; background-color: rgba(0,0,0,0.4); cursor:progress"></div>');
    } else {
        $('#processing').fadeOut(function(){
            $('#processing').remove();
            
        });
    }
    
}

$(document).ready(function(){
    $(".select2").select2();
    

    var options='';
    for (let k in Object.getPrototypeOf(new Loopback())) {
        if (k==='constructor' || k.substr(0,1)==='_')
            continue;
        
        options+='<option value="'+k+'">'+k+'</option>';
    }
    $('select.loopback-functions').append(options);
    $('select.loopback-functions').each(function(){
        $(this).val($(this).attr('v'));
    });
    
    
    
    
    $('form.loopback').each(function(){
        var rel=$(this).attr('rel').split('|');
        var form=this;
        var loopback=new Loopback(rel[0],rel[1]);
        var methodAction=rel[2].split(':');
        
        if (rel[5].length>0 && Object.getPrototypeOf(loopback)[rel[5]]) {
            loopback[rel[5]](rel[3]);
        }
        
        
        $(form).find('button.submit').click(function(ev){
            
            var data={};
            var valid=true;
            $(form).find('input, textarea, select').each(function(){
                var require=$(this).attr('require');
                if ($(this).prop('type')!=='checkbox' || $(this).prop('checked'))   
                    data[$(this).attr('name')] = $(this).val();
                if (!require) 
                    return;
                
                if ($(this).val().trim().length===0) {
                    showErrorsForInput(this,require);
                    valid=false;
                }
                
                
            });
            
            
            if (valid) {
                processing(true);
                loopback._request(methodAction[1],methodAction[0],data,null,function(err,result){
                    processing(false);
                    if (err) {
                        notify(err.message,'danger');
                    } else {
                        if (result && result.message) {
                            notify(result.message,'success');
                            
                        }
        
                        if (rel[6].length>0 && Object.getPrototypeOf(loopback)[rel[6]]) {
                            loopback[rel[6]]({
                                data: data,
                                resp: result,
                                rel: rel
                            }, function(){
                                window.location.href=rel[3];
                            });
                        }
                    }
                });
            }
            
        });
        
        
    
       
    });
    
    
    $('.loopback-list').each(function(){
        let sid=$(this).attr('rel');
      
        if (!window.list || !sid || !window.list[sid])
            return;
        
        var list=window.list[sid];
        
        var loopback=new Loopback(list.root,list.base);
        var methodAction=list.action.split(':');
        
        var columns=[];
        for (var k in list.columns) {
            if (!list.columns[k].label || list.columns[k].label.length===0) 
                continue;
            
            let col= {
                title: list.columns[k].label,
                data: list.columns[k].name
            }
            
            columns.push(col);
        }
        
        var DT;

        $(this).on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
            
            let selected=$(this).closest('table').find('tr.selected').length;
            
            if (selected===0) {
                $(this).closest('.dataTables_wrapper').find('.dt-button.single-select').removeClass('dt-visible');
                $(this).closest('.dataTables_wrapper').find('.dt-button.multi-select').removeClass('dt-visible');
                $(this).closest('.dataTables_wrapper').find('.dt-button.no-select').removeClass('dt-hidden');
            }
            if (selected===1) {
                console.log($(this).closest('.dataTables_wrapper'));
                $(this).closest('.dataTables_wrapper').find('.dt-button.single-select').addClass('dt-visible');
                $(this).closest('.dataTables_wrapper').find('.dt-button.multi-select').addClass('dt-visible');
                $(this).closest('.dataTables_wrapper').find('.dt-button.no-select').addClass('dt-hidden');
            }
            if (selected>1) {
                $(this).closest('.dataTables_wrapper').find('.dt-button.single-select').removeClass('dt-visible');
                $(this).closest('.dataTables_wrapper').find('.dt-button.multi-select').addClass('dt-visible');
                $(this).closest('.dataTables_wrapper').find('.dt-button.no-select').addClass('dt-hidden');
            }
           
        });
        
        var buttons=[];
        
        if (list.buttons.add && list.buttons.add.title) {
            let button={
                text: list.buttons.add.title+' <i class="fa fa-plus"></i>',
                className: 'no-select',
                action: function(e, dt, node, config) {
                    let data=DT.row(DT.$('tr.selected')).data();
                    if (!data)
                        return;
                    let text=list.buttons.copy.text;
                    for (let k in list) {
                        text=text.replace('{'+k+'}',list[k]);
                    }
                    for (let k in data) {
                        text=text.replace('{'+k+'}',data[k]);
                    }
                    navigator.clipboard.writeText(text);
                    notify(text,'info');
                    
                }
            };
            buttons.push(button);
        }
        
        if (list.buttons.copy && list.buttons.copy.title && list.buttons.copy.text) {
            let button={
                text: list.buttons.copy.title+' <i class="fa fa-copy"></i>',
                className: 'single-select',
                action: function(e, dt, node, config) {
                    let data=DT.row(DT.$('tr.selected')).data();
                    if (!data)
                        return;
                    let text=list.buttons.copy.text;
                    for (let k in list) {
                        text=text.replace('{'+k+'}',list[k]);
                    }
                    for (let k in data) {
                        text=text.replace('{'+k+'}',data[k]);
                    }
                    navigator.clipboard.writeText(text);
                    notify(text,'info');
                    
                }
            };
            buttons.push(button);
        }
        
        if (list.buttons.trash && list.buttons.trash.title && list.deleteAction) {
            let button={
                text: list.buttons.trash.title+' <i class="fa fa-trash"></i>',
                className: 'multi-select btn-danger',
                action: function(e, dt, node, config) {
                    let selected=$(node).closest('.dataTables_wrapper').find('tr.selected').length;
                    
                    console.log(selected, list);
                }
            };
            buttons.push(button);
        }
            
        DT=$(this).DataTable({
            dom: 'Bfrtip',
            select: true,
            buttons: buttons,
            processing: true,
            serverSide: true,
            ajax: function(data,cb,settings) {
                var filter={};
                if (data.order) {
                    filter.order='';
                    
                    for (let i=0; i<data.order.length; i++) {
                        filter.order+=data.columns[data.order[i].column].data + ' ' + data.order[i].dir + ' ';
                    }
                }
                
                if (data.start) {
                    filter.offset = data.start;
                }
                if (data.length) {
                    filter.limit = data.length;
                }
                
                if (data.search && data.search.value) {
                    let or=[];
                    for (let i=0; i<data.columns.length; i++) {
                        if (data.columns[i].searchable) {
                            let o={};
                            o[data.columns[i].data] = {like:'%'+data.search.value+'%'}
                            or.push(o)
                        }
                    }
                    filter.where={or:or};
                }
            
                processing(true);
                let header = list.auth==='1'? {authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')} : null;
                loopback._request(methodAction[1],methodAction[0],{filter:filter},header,function(err,result,headers){
                    processing(false);
                    
                    if (err) {
                        notify(err.message,'danger');
                    } else {
                        
                        for (let i=0; i<result.length; i++) {
                            result[i].DT_RowId = result[i].id;
                        }
                
                        cb({
                            draw: data.draw,
                            recordsTotal: headers['x-count-total'],
                            recordsFiltered: result.length,
                            data: result
                        })
                        
                    }
                    
                
                });
                
              
            },
            columns: columns
        });
            
    });

});