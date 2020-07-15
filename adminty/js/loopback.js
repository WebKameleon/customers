  'use strict';
  
  
  
  const Loopback = function(url,prefix) {
    if (!url) return;
    this.url=url;
    
    if (url.substr(url.length-1,1)==='/' && prefix.substr(0,1)==='/') {
        prefix=prefix.substr(1);
    }
    this.url+=prefix;
  }
  
  Loopback.prototype._request=function(url,method,data,header,cb) {
    var finalUrl=this.url+url;
    
    var ajax={
      method: method.toUpperCase(),
      url: finalUrl
    }
    if (header) {
      ajax.headers = header;
    }
    if (ajax.method==='GET') {
      if (data) {
        let str = [];
        for (let k in data) {
          let v = typeof(data[k])==='object' ? JSON.stringify(data[k]) : data[k];
          str.push ( encodeURIComponent(k) + '=' + encodeURIComponent(v) );
        }
        ajax.url+='?'+str.join('&');
      }
      
    } else {
      ajax.data=data;
    }
    
    
    var jqxhr = $.ajax(ajax)
        .done(function(data,txt,req) {
          let headers={};
          let resp=req.getAllResponseHeaders().split("\n");
          for (let i=0; i<resp.length; i++) {
            let r=resp[i].split(':');
            if (r.length===2) 
              headers[r[0]] = r[1].trim();
          }
          cb(null,data,headers);
        })
        .fail(function(e) {
          cb(e.responseJSON);
          if (e.responseJSON.code && e.responseJSON.code==='AUTHORIZATION_REQUIRED') {
            Loopback.prototype.logout();
          }
        });
  }
  
  
  Loopback.prototype.login = function(data,cb) {
    if (data.resp.token) {
        window.localStorage.setItem('swagger_accessToken',data.resp.token);
    }
    window.localStorage.setItem('remember_accessToken',data.data.remember);
    
    if (data.resp.me)
      window.localStorage.setItem('me',JSON.stringify(data.resp.me));

    if (cb)
      cb();
  }
  
  Loopback.prototype.logout = function(link,cb) {
    if (!link) {
      $('.icon-log-out').closest('a').each(function(){
        link=this;
      });
    }
    let logoutAction=Loopback.logoutInstance.logoutOptions.action;
    let header=Loopback.logoutInstance.logoutOptions.header;
    processing(true);
    Loopback.logoutInstance._request(logoutAction[1],logoutAction[0],null,header,function(err,result){
      processing(false);
      window.localStorage.removeItem('swagger_accessToken');
      window.localStorage.removeItem('remember_accessToken');
      window.localStorage.removeItem('me');
      if (cb)
        cb();
      else
        location.href=$(link).attr('href');
    });
  
    
  }
  
  Loopback.prototype.goNext = function(data,cb) {
    if (data.rel[3] && data.rel[3].length) 
      location.href=data.rel[3];
  }
  
  Loopback.prototype.url2form = function(data,form,cb) {
    
    var url=location.search;
    var query = url.substr(1);
    var result = {};
    query.split("&").forEach(function(part) {
      var item = part.split("=");
      if (item[1]) {
        result[item[0]] = decodeURIComponent(item[1]);
      }
    });
    for (var k in result) {
      $(form).find('[name="'+k+'"]').val(result[k]);
    }
  }
  
  Loopback.prototype.reload = function(data,cb) {
    location.reload();
  }
  
  
  Loopback.prototype.init = function(formUrl,cb) {
    var verification=getUrlParameter('verification');
    //console.log(verification);
    if (verification) {
      var verify=verification.split('/');
      var data=verify[verify.length-1];
      verify.splice(verify.length-1,1);
      this._request(verify.join('/'),'POST',{verification:data},null,function(err,result){
        if (err) {
            notify(err.message,'danger');
        } else {
            if (result && result.message) {
                notify(result.message,'success');
            }
        }
      })
    }

    if (window.localStorage.getItem('remember_accessToken') && window.localStorage.getItem('swagger_accessToken') ){
        window.location.href=formUrl;
    }
  }
  
  Loopback.prototype._initAdminity = function(logoutAction, header) {
    Loopback.logoutInstance=this;
    this.logoutOptions={
      action: logoutAction,
      header: header
    }
    
    $('.icon-log-out').closest('a').click(function(ev){
      Loopback.logoutInstance.logout(this);
      return false;
    });
    
    var me=window.localStorage.getItem('me');
    if (me) {
      me=JSON.parse(me);
      $('.username').html(me.username||me.firstName+' '+me.lastName);
      if (me.roles && me.roles.length) {
        
        for (let i=0; i<me.roles.length; i++)
          if (me.roles[i].name==='admin') {
            $('.admin-access').addClass('user-admin');
          }
      }
      
      
    } else if (adminityKameleonMode<2) {
      Loopback.logoutInstance.logout();
    }
    
  }
  
  
  
