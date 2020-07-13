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
        });
  }
  
  
  Loopback.prototype.login = function(data,cb) {
    if (data.resp.token) {
        window.localStorage.setItem('swagger_accessToken',data.resp.token);
    }
    window.localStorage.setItem('remember_accessToken',data.data.remember);
    
    if (cb)
        cb();
  }
  
  Loopback.prototype.logout = function(data,cb) {
    window.localStorage.removeItem('swagger_accessToken');
  }
  
  Loopback.prototype.goNext = function(data,cb) {
    if (data.rel[3] && data.rel[3].length) 
      location.href=data.rel[3];
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
    return;
    if (window.localStorage.getItem('remember_accessToken') && window.localStorage.getItem('swagger_accessToken') ){
        window.location.href=formUrl;
    }
  }
  
  
