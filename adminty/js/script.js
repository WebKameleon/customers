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

function setLocation(url,id,qs) {
    if (id) {
        if (url.substr(url.length-1,1)!=='/')
            url+='/';
        url+=id;
    }
    if (qs) {
        url+=url.indexOf('?')>0?'&':'?';
        url+=qs;
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


function historyReplace(p,v,list,t) {
    let lh=location.href.replace(location.origin,'');
    const re=new RegExp(p+'=[^&]*');
    lh=lh.replace(re,p+'='+encodeURIComponent(v));
    if (lh.indexOf(p+'=')===-1) {
        lh+=lh.indexOf('?')===-1 ? '?' : '&';
        lh+=p+'='+encodeURIComponent(v);
    }
    history.pushState({}, '', lh);
    $(list).attr('ret',btoa(lh+(t?'#'+t:'')));
}

function setLocationUrlRet(url, onlyReturn) {
    var ret=getUrlParameter('ret');
    if (ret)
        url=atob(ret);
    
    if (onlyReturn) {
        return url;
    }
    location.href = url;
    return false;
}


function smartSort(arr) {
    arr.sort(function(a,b){
        for (let k in a) {
            if (k==='id' || typeof a[k]!=='number')
                continue;
            
            if (b[k]) {
                if (a[k]>b[k])
                    return 1;
                if (a[k]<b[k])
                    return -1;
                
                return 0;
                
            } else {
                return 1;
            }
            
        }
        
        return 0;
    });
}


function smekta(text,obj) {
    for (let k in obj) {
        const re=new RegExp('{+'+k+'}+','g');
        text=text.replace(re,obj[k]);
    }
    
    return text;
}


function timeShrink(t) {    
    if (t<60) {
        return t+' s';
    }
    if (t<3600) {
        return Math.round(t/60)+' m';
    }
    if (t<24*3600) {
        return Math.round(10*t/3600)/10+' h';
    }
    
    
    return Math.round(t*10/(24*3600))/10+' d';
}

$(document).ready(function(){
    $(".select2").select2();

    
    var href=location.href.toString().split('?')[0].split('/');
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
                    
                    if (result[k]) {
                        
                        if (typeof result[k]==='object') {
                            result[k] = JSON.stringify(result[k],null,2);
                        }
                        
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
                    
                    
                    $(form).find('input.range-slider[name="'+k+'"]').each(function(){
                        
                        var slider = new Slider(this, {
                            step: parseFloat($(this).attr('data-slider-step')),
                            min: parseFloat($(this).attr('data-slider-min')),
                            max: parseFloat($(this).attr('data-slider-max')),
                            tooltip: 'always',
                            value: result[k]||$(this).val()
                        });
                    });
                }
                getSelects(loopback,form,urlID,requestHeader,function(){
                    processing(false);
                });
                
                if (rel[8] && result[rel[8]] && rel[3]) {
                    if (rel[3].slice(-1)!=='/') 
                        rel[3]+='/'; 
                    rel[3]+=result[rel[8]];
                }
                
                $('.init').each(function(){
                    
                    var href=$(this).attr('href');
                    var html=$(this).html();
                    
                    for (let k in result) {
                        if (href) 
                            href=href.replace('{{'+k+'}}',result[k]);
                        if (html)
                            html=html.replace('{{'+k+'}}',result[k]);
                    }
                    
                    if (href)
                        $(this).attr('href',href);
                    if (html)
                        $(this).html(html);
                    
                    $(this).show();
                });
                
            });
        }
        
        $(form).find('button.return').click(function(ev){
            if (rel[3]) {
                setLocationUrlRet(rel[3]);
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
                        
                        rel[3] = setLocationUrlRet(rel[3],true);
        
                        if (rel[6].length>0 && Object.getPrototypeOf(loopback)[rel[6]]) {
                            loopback[rel[6]]({
                                data: data,
                                resp: result,
                                rel: rel
                            }, function(){
                                setLocationUrlRet(rel[3]);
                            });
                        } 
                    }
                });
            }
            
        });
        
        
    
       
    });
    
    $(document).on('click','.clipboard', function(){
        let text=$(this).attr('href');
        navigator.clipboard.writeText(text);
        notify(text,'info');
        return false;
    });
    
    $('.loopback-list').each(function(){
        let sid=$(this).attr('rel');
        const self=this;
        
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
        var checkboxes = {};
        
        for (var k in list.columns) {
            if (!list.columns[k].label || list.columns[k].label.length===0) 
                continue;
                
            let col= {
                tooltip: list.columns[k].title,
                data: list.columns[k].name.replace(':','.'),
                type: list.columns[k].type,
                name: list.columns[k].label,
                title: list.columns[k].label
            }
            
            
            if (list.columns[k].type==='string') {
                col.searchable='like';
            } else if (list.columns[k].type==='double' && list.columns[k].name.indexOf('Count')===-1) {
                col.searchable='eq';
            } else if (list.columns[k].type.indexOf('date')!==-1) {
                col.searchable=false;
            } else {
                col.searchable=false;
                col.bSortable=false;
            }
            
            if (list.columns[k].type==='boolean' && list.columns[k].editable) {
                checkboxes[k] = {i: columns.length, list:list.columns[k], checked:true};
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
                        setLocation(list.next,data.id,list.follow===1?'ret='+$(self).attr('ret'):null);    
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
        
        if (list.buttons.custom && list.buttons.custom.title && list.customAction) {
            let text = list.buttons.custom.title;
            if (list.buttons.custom.icon)
                text+=' <i class="fa fa-'+list.buttons.custom.icon+'"></i>';
            let className='multi-select';
            if (list.buttons.custom.class)
                className+=' btn-'+list.buttons.custom.class;
            let button={
                text: text,
                className: className,
                action: function(e, dt, node, config) {
                    let data=[];
                    
                    DT.$('tr.selected').each(function(i,dt){
                        data.push(DT.row(dt).data());
                    });
                    
                    processing(true);
                    async.map(data,function(row,next){
                        let action=list.customAction;
                        for (let k in row) {
                            action=action.replace('{'+k+'}',row[k]);
                        }
                        let methodAction=action.split(':');
                        loopback._request(methodAction[1],methodAction[0],null,requestHeader,function(err,result){
                            next();
                        });
                        
                    },function(){
                        processing(false);
                    });
                    
                    
                    
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
        
        var o=getUrlParameter('o');
        if (o) {
            order=[o.replace(/[^0-9]/,''),o.indexOf('-')>0?'desc':'asc'];
        }
        
        var dataTableOptions={
            dom: 'Bfrtip',
            select: true,
            buttons: buttons,
            processing: true,
            serverSide: true,
            className:'wrap',
            pageLength: list.size||10,
            order: [order],
            ajax: function(data,cb,settings) {
                var filter={};
                if (data.order) {
                    filter.order='';
                    
                    for (let i=0; i<data.order.length; i++) {
                        filter.order+=data.columns[data.order[i].column].data + ' ' + data.order[i].dir + ' ';
                    }
                    historyReplace('o',data.order[0].column+(data.order[0].dir==='desc'?'-':''),self,list.title);
                }
                
                if (data.start) {
                    filter.offset = data.start;
                }
                if (data.length) {
                    filter.limit = data.length;
                    
                    historyReplace('p',1+Math.floor(data.start/data.length),self,list.title);
                }
                
                for (var k in list.columns) {
                    if (list.columns[k].label && list.columns[k].label.length>0 && list.columns[k].relation && list.columns[k].relation.length>0) {
                        if (!filter.count) {
                            filter.count=[];
                        }
                        filter.count.push(list.columns[k].relation);
                    }
                }
                
                //console.log(filter);
               
               
                
                if (data.search && data.search.value) {
                    
                    historyReplace('q',data.search.value,self,list.title);
                    
                    let q=data.search.value;
                    for (let k in window.card ) {
                        if (window.card[k].q) {
                            if (q.indexOf(' ')===-1 && q.indexOf(':')===-1) {
                                window.card[k].q(q);
                            } else {
                                window.card[k].q();
                            }
                        }
                    }
                    q=data.search.value.split(' ');
                    let and=[];
                    for (let j=0; j<q.length; j++) {
                        if (q[j].length===0)
                            continue;
                
                        let word=q[j].match(/([a-zA-Z0-9 ]+)([:=><!]+)([a-zA-Z0-9\-]+)/);
                    
                        
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
                    
                } else {
                    historyReplace('q','',self,list.title);
                    for (let k in window.card ) {
                        window.card[k].q&&window.card[k].q();
                    }
                }
                
                for (let k in checkboxes)
                    checkboxes[k].checked = true;
                    
                if (list.include && list.include.length && list.include.length>0) {
                    filter.include = list.include.split(',');
                }
                
                loopback._request(methodAction[1],methodAction[0],{filter:filter},requestHeader,function(err,result,headers){
                    
                    if (err) {
                        notify(err.message,'danger');
                    } else {
                        
                    
                        for (let i=0; i<result.length; i++) {
                            if (result[i]===null) {
                                result.splice(i--,1);
                                continue;
                            }
                            result[i].DT_RowId = result[i].id;
                            
                            for (let k in result[i]) {
                                if (list.relations && list.relations[k]) {
                                    for (let kk in list.relations[k].fields) {
                                        result[i][k+'.'+kk] = result[i][k][kk];  
                                    }
                                }
                            }
                            
                            
                            for (let k in result[i]) {
                                
                                if (list.columns[k] && list.columns[k].editable && list.columns[k].editable.length) {
                                    if (list.columns[k].type.indexOf('boolean')!==-1) {
                                        
                                        var checked=result[i][k]?'checked':'';
                                        
                                        if (!result[i][k] && checkboxes[k]) {
                                            checkboxes[k].checked = false;
                                        }
                                        
                                        var chid = sid + '-' + list.columns[k].name + '-' + result[i].id;
                                        var html='<div class="checkbox-zoom zoom-primary list-editable-checkbox" rel="'+chid+'|'+result[i].id+'|'+list.columns[k].editable+'">';
                                        html+='<label><input type="checkbox" id="'+chid+'" '+checked+' class="checkbox-'+k+'"/>';
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
                        });
                        
                        
                        
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
                
                const header = DT.columns().header();
                for (let i=0; i<header.length; i++) {
                    //console.log(header[i], columns);
                    for (let j=0; j<columns.length; j++) {
                        if (columns[j].title==header[i].innerHTML) {
                            if (columns[j].tooltip) {
                                $(header[i]).attr('title',columns[j].tooltip);
                            }
                            
                            
                        }
                    }
                }
            }
        }
        
        var q=getUrlParameter('q');
        if (q && q.length>0){
            dataTableOptions.oSearch={"sSearch": q};
        }
        var p=getUrlParameter('p');
        if (p && p.length>0) {
            dataTableOptions.displayStart=(parseInt(p)-1)*dataTableOptions.pageLength;
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
                    notify('OK','success');
                    
            });
            
            
        });
        

        
        DT.columns().header().on('click','.checkbox-all', function(){
     
            var rel=$(this).attr('rel');
            var checked = $(this).prop('checked');
            
            $(this).closest('table').find('.checkbox-'+rel).each(function(){
                if ($(this).prop('checked') === checked)
                    return;
                var rel=$(this).closest('.list-editable-checkbox').attr('rel').split('|');
                var id=rel[1];
                var action=rel[2].toLowerCase().split(',');
                action = checked ? action[0] : action[1];
                
                action=list[action+'Action'];
                if (!action)
                    return;
                let methodAction=action.replace('{id}',urlID).split(':');
                
                loopback._request(methodAction[1],methodAction[0],{data:{id:id}},requestHeader,function(err,result){
                    processing(false);
                    if (result && result.id)
                        notify('OK','success');
                        
                        
                });

                $(this).prop('checked',checked);
            });
            
            
            
        });
        
        
        for (let k in checkboxes) {           
            if (checkboxes[k].list.editable.indexOf('all')===-1 )
                continue;
            let header = DT.columns().header()[checkboxes[k].i];
            let html='<input type="checkbox" class="checkbox-all checkbox-all-'+k+'" rel="'+k+'"/> '+$(header).html();
            $(header).html(html);
            
        }
        
        DT.on('draw.dt',function(){
            for (let k in checkboxes) {
                $('input.checkbox-all-'+k).prop('checked',checkboxes[k].checked);
            }
        });
        
        
    });
    
    $('.loopback-card').each(function(){
        const sid=$(this).attr('rel');
        const self=this;
        
        if (!window.card || ! window.card[sid])
            return;
        
        const card = window.card[sid];
        const requestHeader = card.auth==='1'? {authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')} : null;
        const loopback=new Loopback(card.root,card.base);
        const action=card.action;
        const methodAction=action.split(':');
        
        const change=card.change && card.change.length>0?card.change.split(','):null;
        const html = $(self).html();
        const classValue=$(self).attr('class').replace('loopback-card','');
        
        window.card[sid].q = function (q) {
            let data=null;
            for (let k in card.params) {
                if (card.params[k].label && card.params[k].label.length) {
                    if (!data)
                        data={};
                    data[k] = card.params[k].label.replace('{id}',urlID);
                    continue;
                }
                
                if (k==='q' && q && q.length>0) {
                    data[k]=q;
                }
                
            }
                
            
            const resultHtml=[];
            
            loopback._request(methodAction[1],methodAction[0],data,requestHeader,function(err,result){
                if (err || !result || !result.result)
                    return;
                if (Array.isArray(result.result)) {
                    smartSort(result.result);
                    
                    for (let i=0; i<result.result.length; i++) {
                        result.result[i].change=0;
                        result.result[i].color='pink';
                        result.result[i].icon=card.icon||'calendar';
                        
                        
                        if (result.result[i].main && result.result[i].id) {
                            
                            let changeAbs=0, changePrc=0;
                            if (result.diffrence && Array.isArray(result.diffrence)) {
                                for (let j=0; j<result.diffrence.length; j++) {
                                    if (result.diffrence[j].id && result.result[i].id===result.diffrence[j].id && result.diffrence[j].main) {
                                        changeAbs=result.result[i].main-result.diffrence[j].main;
                                        changePrc = Math.ceil(changeAbs*100 / result.diffrence[j].main);
                                    }
                                }
                            }
                            
                            result.result[i].change = changeAbs;
                            
                            if (changePrc!==0) {
                                if (changePrc>0) {
                                    result.result[i].color='blue';
                                }
                                if (changePrc>5) {
                                    result.result[i].color='yellow';
                                }
                                if (changePrc>10) {
                                    result.result[i].color='green';
                                }
                            }
                            
                            if (change) {
                                for (let j=0; j<change.length; j++) {
                                    let changePair=change[j].split(':');
                                    if (changePair.length===2 && result.result[i][changePair[0]] && window[changePair[1]]) {
                                        result.result[i][changePair[0]] = window[changePair[1]](result.result[i][changePair[0]]);    
                                    }
                                    
                                    if (changePair[0]==='change' && changePair[1]==='prc') {
                                        result.result[i].change = changePrc+'%';
                                    }
                                    
                                    
                                }
                                
                            }
                            
                            if (typeof(result.result[i].change)==='number') {
                                result.result[i].change = result.result[i].change.toString();
                            }
                            
                            if (result.result[i].change==='0') {
                                result.result[i].change='';
                            } else if (result.result[i].change.indexOf('-')===-1) {
                                result.result[i].change = '+'+result.result[i].change;
                            }
                            
                            resultHtml.push('<div class="'+classValue+' card'+sid+'">'+smekta(html,result.result[i])+'</div>');
                        }
                      
                        
                    }
                    const size=card.size.length?parseInt(card.size):0;
                    while (Array.isArray(result.result) && size>0 && resultHtml.length>size) {
                        resultHtml.splice(0,1);
                    }
                    resultHtml.push('<div class="clearfix"/>');
                }
                
                while ($('.card'+sid).length>1) {
                    $('.card'+sid).first().remove();
                }
                if($('.card'+sid).length>0)
                    $('.card'+sid).replaceWith(resultHtml.join(''));
                else
                    $(self).replaceWith(resultHtml.join(''));
            });
        };
        
        window.card[sid].q(getUrlParameter('q'));   
        
    });
    
    $('.google-chart').each(function(){
        const sid=$(this).attr('rel');
        const self=this;
        const id=$(this).attr('id');

        if (!window.chart)
            return;
        const chart=window.chart[id];
        const requestHeader = chart.auth==='1'? {authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')} : null;
        const loopback=new Loopback(chart.root,chart.base);
        const action=chart.action;
        const methodAction=action.split(':');

        window.chart[id].q = function (q) {
            let data=null;
            for (let k in chart.params) {
                if (chart.params[k].label && chart.params[k].label.length) {
                    if (!data)
                        data={};
                    data[k] = chart.params[k].label.replace('{id}',urlID);
                    continue;
                }
                if (k==='q' && q && q.length>0) {
                    data[k]=q;
                }
                
            }
                
            
            
            loopback._request(methodAction[1],methodAction[0],data,requestHeader,function(err,result){
                if (err || !result || !result.result || !Array.isArray(result.result) || result.result.length===0)
                    return;
                
                smartSort(result.result);
   
                const dataTable = [];
                const header=[chart.options.vAxis.title];
                for (let i=0;i<chart.series.length; i++) {
                    if (!chart.series[i].label || chart.series[i].label.length===0)
                        break;
                    header.push(chart.series[i].label);
                }
                dataTable.push(header);
                
                for (let i=0; i<result.result.length; i++) {
                    const row=[
                        smekta(chart.series[0].vValue,result.result[i])
                    ];
                    for (let j=0; j<chart.series.length; j++) {
                        if (!chart.series[j].label || chart.series[j].label.length===0)
                            break;
                        
                        row.push(result.result[i][chart.series[j].hValue] || 0);
                        
                    }
                    dataTable.push(row);
                }
                
          
                
                
                google.charts.load('current', {packages: chart.packages?chart.packages.split(','):['corechart', 'bar']});
        
                google.charts.setOnLoadCallback(function () {
                    data=google.visualization.arrayToDataTable(dataTable);
                    
                    console.log(chart.options);
                    chart.options.chartArea.height = '92%';
                    $('#'+id).height(200+50*dataTable.length);
                    
                    
                    chart.chart = new google.visualization.BarChart(document.getElementById(id));
                    chart.chart.draw(data, chart.options);
                  });
                
                
            });
        };
        
        window.chart[id].q(getUrlParameter('q'));
        
        
        
        
    });

});

