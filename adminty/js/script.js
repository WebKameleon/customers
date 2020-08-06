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

function notify(message, type, delay){
    return $.growl({
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
        delay: delay||5000,
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

var processingNotification=null;

function processing(on) {
    if (on) {
        processingNotification=notify('Please wait','info',60000);
    
        $('body').append('<div id="processing" style="position:absolute; top:0; left:0; height:100%; width:100%; z-index: 99999999; background-color: rgba(0,0,0,0.4); cursor:progress"></div>');
    } else {
        if (processingNotification) {
            processingNotification.close();
            processingNotification=null;
        }
        $('#processing').fadeOut(function(){
            $('#processing').remove();
        });
    }
    
}

function setLocation(url,id) {
    if (id) {
        if (url.substr(url.length-1,1)!=='/')
            url+='/';
        url+=id;
    }
    location.href=url;
}

function getSelects(loopback,form,urlID,requestHeader,cb) {
    var selects=$(form).find('.loopback-form-select');
    if (selects.length===0)
        return cb();
    
    selects.each(function(){
        var select=this;
        var rel=$(this).attr('rel').split('|');
        var action=rel[0].replace('{id}',urlID).split(':');
        var filter={filter:{order:rel[1]}};
        
        loopback._request(action[1],action[0],filter,requestHeader,function(err,result){
            if (err) {
                notify(err.message,'danger');
                return;
            }
            if (!Array.isArray(result))
                return;
            
            for (var i=0; i<result.length; i++) {
                var selected=result[i][rel[2]]==$(select).attr('v') ? 'selected' : '';
                if (typeof result[i].current !== 'undefined') {
                    selected=result[i].current?'selected ':'';
                }
                $(select).append('<option '+selected+' value="'+result[i][rel[2]]+'">'+result[i][rel[1]]+'</option>');
            }
          
            cb();
        });
    });
}


$(document).ready(function(){
    $(".select2").select2();
    
    var href=location.href.toString().split('/');
    var urlID = parseInt(href[href.length-1]);
    if (isNaN(urlID)) 
        urlID=null;
    
    
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
    
    
    $('div.profile').each(function(){
        var rel=$(this).attr('rel').split('|');
        var loopback=new Loopback(rel[0],rel[1]);
        var logoutAction=rel[2].split(':');
        loopback._initAdminity(logoutAction,{authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')});
    });
    
    $('form.loopback').each(function(){
        var form=this;
        var rel=$(this).attr('rel');
        rel=rel.replace(/\{id\}/g,urlID);
        rel=rel.split('|');
        var loopback=new Loopback(rel[0],rel[1]);
        var methodAction=rel[2].split(':');
        
        let requestHeader = rel[4]==='1'? {authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')} : null;
       
        
        if (rel[5].length>0 && Object.getPrototypeOf(loopback)[rel[5]]) {
            loopback[rel[5]](rel[3],form);
        }
        
        
        if (rel[7].length>0) {
            var initAction=rel[7].split(':');
            processing(true);
            loopback._request(initAction[1],initAction[0],null,requestHeader,function(err,result){
                if (err) {
                    notify(err.message,'danger');
                    return;
                }
                for (let k in result) {
                    $(form).find('input[type="text"][name="'+k+'"]').val(result[k]).attr('v',result[k]);
                    $(form).find('select[name="'+k+'"]').val(result[k]).attr('v',result[k]);
                    $(form).find('textarea[name="'+k+'"]').val(result[k]).attr('v',result[k]);
                    $(form).find('input[type="hidden"][name="'+k+'"]').each(function(){
                        if ($(this).attr('rel')!='checkbox') {
                            $(this).val(result[k]).attr('v',result[k]);
                        }
                    });
                    $(form).find('input[type="checkbox"][name="'+k+'"]').each(function(){
                        if (result[k]) {
                            $(this).prop('checked',true);
                        }
                    });
                }
                getSelects(loopback,form,urlID,requestHeader,function(){
                    processing(false);
                });
                
                if (rel[8] && result[rel[8]] && rel[3]) {
                    rel[3]+='/'+result[rel[8]];
                }
                
            });
        }
        
        $(form).find('button.return').click(function(ev){
            if (rel[3]) {
                location.href=rel[3];
            }
        });
        
        
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
                loopback._request(methodAction[1],methodAction[0],data,requestHeader,function(err,result){
                    processing(false);
                    if (err) {
                        notify(err.message,'danger');
                    } else {
                        if (result && result.message) {
                            notify(result.message,'success');
                        } else {
                            notify('OK','success');
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
        
        if ($(this).hasClass('admin-access') && !$(this).hasClass('user-admin')) {
            return;
        }
      
        if (!window.list || !sid || !window.list[sid])
            return;
        
        var list=window.list[sid];
        let requestHeader = list.auth==='1'? {authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')} : null;
        
        var loopback=new Loopback(list.root,list.base);
        var methodAction=list.action.replace('{id}',urlID).split(':');
        
        var columns=[];
        for (var k in list.columns) {
            if (!list.columns[k].label || list.columns[k].label.length===0) 
                continue;
            
            let col= {
                title: list.columns[k].label,
                data: list.columns[k].name.replace(':','.'),
                type: list.columns[k].type,
                name: list.columns[k].label
            }
            
            if (list.columns[k].type==='string') {
                col.searchable='like';
            } else if (list.columns[k].type==='double') {
                col.searchable='eq';
            } else {
                col.searchable=false;
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
        
        if (list.buttons.add && list.buttons.add.title && list.postAction) {
            let button={
                text: list.buttons.add.title+' <i class="fa fa-plus"></i>',
                className: 'no-select',
                action: function(e, dt, node, config) {
                    let action=list.postAction;
                    let methodAction=action.split(':');
                    
                    function add(data) {
                        processing(true);
                        loopback._request(methodAction[1],methodAction[0],data,requestHeader,function(err,result){
                            processing(false);
                            if (result && result.id)
                                setLocation(list.next,result.id);
                        });                        
                    }
                    
                    if (list.buttons.add.init && window[list.buttons.add.init]) {
                        window[list.buttons.add.init](function(err,data){
                            if (err || !data) {
                                
                            } else {
                                add(data);
                            }
                        });
                    } else {
                        add({});
                    }

                    
                    
                }
            };
            buttons.push(button);
        }
        
        if (list.buttons.edit && list.buttons.edit.title ) {
            let button={
                text: list.buttons.edit.title+' <i class="fa fa-edit"></i>',
                className: 'single-select btn-info',
                action: function(e, dt, node, config) {
                    let data=DT.row(DT.$('tr.selected')).data();
                    if (!data)
                        return;
                    
                    if (list.putAction) {
                        let action=list.putAction;
                        for (let k in data) {
                            action=action.replace('{'+k+'}',data[k]);
                        }
                        let methodAction=action.split(':');
                        processing(true);
                        loopback._request(methodAction[1],methodAction[0],null,requestHeader,function(err,result){
                            processing(false);
                            setLocation(list.next,result.id);
                        });
                    } else {
                        setLocation(list.next,data.id);    
                    }
                    
                    
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
        
        if (list.buttons.popup && list.buttons.popup.title && list.buttons.popup.url) {
            let button={
                text: list.buttons.popup.title+' <i class="fa fa-window-restore"></i>',
                className: 'single-select',
                action: function(e, dt, node, config) {
                    let data=DT.row(DT.$('tr.selected')).data();
                    if (!data)
                        return;
                    
                    const w=parseFloat(list.buttons.popup.width||800);
                    const h=parseFloat(list.buttons.popup.height||600);
                    let url=list.buttons.popup.url;
                    
                    for (let k in list) {
                        url=url.replace('{'+k+'}',list[k]);
                    }
                    for (let k in data) {
                        url=url.replace('{'+k+'}',data[k]);
                    }
                    
                    const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                    const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                    const systemZoom = width / window.screen.availWidth;
                    
                    const left = (width - w) / 2 / systemZoom;
                    const top = (height - h) / 2 / systemZoom;
                    let p=window.open(url, list.buttons.popup.title, 'scrollbars=yes,width='+w+',height='+h+',left='+left+',top='+top);
                    
                }
            };
            buttons.push(button);
        }
        
        if (list.buttons.trash && list.buttons.trash.title && list.deleteAction) {
            let button={
                text: list.buttons.trash.title+' <i class="fa fa-trash"></i>',
                className: 'multi-select btn-danger',
                action: function(e, dt, node, config) {
                    let data=[];
                    
                    DT.$('tr.selected').each(function(i,dt){
                        data.push(DT.row(dt).data());
                    });
                    
                    function deleteArray(rows) {
                        processing(true);
                        async.map(rows,function(row,next){
                            let action=list.deleteAction;
                            for (let k in row) {
                                action=action.replace('{'+k+'}',row[k]);
                            }
                            let methodAction=action.split(':');
                            loopback._request(methodAction[1],methodAction[0],null,requestHeader,function(err,result){
                                console.log(err,result);
                                next();
                            });
                            
                        },function(){
                            processing(false);
                            DT.draw();
                        });
                    }
                    
                    if (!list.buttons.trash.confirm || list.buttons.trash.confirm.length===0)
                        return deleteArray(data);
                    
                    if (confirm(list.buttons.trash.confirm+' ('+data.length+')?'))
                        return deleteArray(data);
                }
            };
            buttons.push(button);
        }
        
        
        function filterParse(type,value,op) {
            if (value.length===0)
                return null;
            
            if (type==='like') {
                if (op==='!=' || op==='<>')
                    return {nlike:'%'+value+'%'};
                return {like:'%'+value+'%'};
            }
            
            if (!op || op===':' || op==='=' || op==='==') {
                if (isNaN(parseFloat(value)))
                    return null;
                return value;
            }
            
            if (op==='!=' || op==='<>') {
                if (isNaN(parseFloat(value)))
                    return null;
                return {neq:value};
            }
                
            if (op==='>')
                return {gt:value};
            if (op==='<')
                return {lt:value};
            if (op==='>=')
                return {gte:value};
            if (op==='<=')
                return {lte:value};
            
            
            
            return value;
        }
        
        
        let order=list.order.split(',');
        if (order.length===1) {
            order=[0,'asc'];
        }
        var dataTableOptions={
            dom: 'Bfrtip',
            select: true,
            buttons: buttons,
            processing: true,
            serverSide: true,
            className:'wrap',
            order: [order],
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
                    
                    let q=data.search.value.split(' ');
                    let and=[];
                    for (let j=0; j<q.length; j++) {
                        if (q[j].length===0)
                            continue;
                
                        let word=q[j].match(/([a-zA-Z0-9 ]+)([:=><!]+)([a-zA-Z0-9\-]+)/);
                        
                        console.log(word);
                        
                        if (!word) {
                            let or=[];
                            for (let i=0; i<data.columns.length; i++) {
                                if (data.columns[i].searchable) {
                                    
                                    let v=filterParse(data.columns[i].searchable,q[j]);
                                    if (v!==null) {
                                        let o={};
                                        o[data.columns[i].data] = v;
                                        or.push(o);
                                    }
                                     
                                }
                            }
                            if (or.length>0) {
                                and.push({or:or});
                            }
                            
                        } else {
                            for (let i=0; i<data.columns.length; i++) {
                              
                                if (data.columns[i].name.toLowerCase() === word[1].toLowerCase()) {
                                    
                                    let v=filterParse(data.columns[i].searchable,word[3],word[2]);
                                    if (v!==null) {
                                        let a={};
                                        a[data.columns[i].data] = v;
                                        and.push(a);
                                    }
                                    break;
                                }
                                    
                                
                            }
                        }
                        
                    }
                    
                    if (and.length>0) {
                        filter.where={and:and};
                    }
                    
                }
                                
                loopback._request(methodAction[1],methodAction[0],{filter:filter},requestHeader,function(err,result,headers){
                    
                    if (err) {
                        notify(err.message,'danger');
                    } else {
                        
                    
                        for (let i=0; i<result.length; i++) {
                            result[i].DT_RowId = result[i].id;
                            for (let k in result[i]) {
                                
                                if (list.columns[k] && list.columns[k].editable && list.columns[k].editable.length) {
                                    if (list.columns[k].type.indexOf('boolean')!==-1) {
                                        
                                        var checked=result[i][k]?'checked':'';
                                        var chid = sid + '-' + list.columns[k].name + '-' + result[i].id;
                                        var html='<div class="checkbox-zoom zoom-primary list-editable-checkbox" rel="'+chid+'|'+result[i].id+'|'+list.columns[k].editable+'">';
                                        html+='<label><input type="checkbox" id="'+chid+'" '+checked+'/>';
                                        html+='<span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div>';
                                        
                                        result[i][k] = html;
                                    }
                                    
                                } else if (list.columns[k] && list.columns[k].type) {
                                    if (list.columns[k].type.indexOf('date')!==-1)
                                        result[i][k] = moment(new Date(result[i][k])).format('DD-MM-YYYY HH:mm');
                                        
                                    if (list.columns[k].type.indexOf('boolean')!==-1)
                                        result[i][k] = '<i class="fa '+(result[i][k]?'fa-check-square-o':'fa-square-o')+'"></i>';
                                    
                                    if (list.columns[k].type.indexOf('object')!==-1)
                                        result[i][k] = result[i][k]?'<textarea class="json">'+JSON.stringify(result[i][k])+'</textarea>':'';
                                }
                            }
                        }
                    
                
                        cb({
                            draw: data.draw,
                            recordsTotal: headers['x-count-total'],
                            recordsFiltered: headers['x-count-total'],
                            data: result
                        })
                        
                    }
                    
                
                });
                
              
            },
            columns: columns,
            drawCallback: function(settings) {
                $(settings.nTable).find('textarea.json').each(function(){
                    $(this).parent().jsonViewer(JSON.parse($(this).val()),{
                        collapsed: true
                    });
                });
            }
        }
        
        var q=getUrlParameter('q');
        if (q && q.length>0){
            dataTableOptions.oSearch={"sSearch": q};
        }
        
        DT=$(this).DataTable(dataTableOptions);
        $(this).on('click','.list-editable-checkbox .cr', function(){
            var rel=$(this).closest('.list-editable-checkbox').attr('rel').split('|');
            
            var id=rel[1];
            var action=rel[2].toLowerCase().split(',');
            var ch=$('#'+rel[0]).prop('checked');
            action = ch ? action[1] : action[0];
            
            action=list[action+'Action'];
            if (!action)
                return;
            
            let methodAction=action.replace('{id}',urlID).split(':');
            processing(true);
            loopback._request(methodAction[1],methodAction[0],{data:{id:id}},requestHeader,function(err,result){
                processing(false);
              
                if (result && result.id)
                    setLocation(list.self,urlID);
                    
            });
            
            
        });
    });
    
    
    

});