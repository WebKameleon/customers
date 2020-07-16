"use strict";

function drivePicker(cb) {
    if (!googleDrive.access || !googleDrive.access.token) {
        return $('#'+googleDrive.buttonId).click();
    }
    
    const mainView = new google.picker.View(google.picker.ViewId.DOCS).setMimeTypes('application/vnd.google-apps.spreadsheet');

    new google.picker.PickerBuilder()
      .addView(mainView)
      .setAppId(googleDrive.access.appId)
      .setOAuthToken(googleDrive.access.token.access_token)
      .setCallback(function(result){
        if (result.action==='picked' && result.docs && result.docs.length>0) {
            cb(null,{id:result.docs[0].id});
        }
        
      }).setLocale(adminityKameleonLang).build().setVisible(true);
    
}

function loadPicker() {
   
    gapi.load('picker', {
        callback: function () {
        }
    });
    
}


function drive() {
	if (document.readyState!=='complete')
		return setTimeout(drive,100);
	
	if (!googleDrive || !googleDrive.buttonId || !googleDrive.getAction || !googleDrive.postAction) {
		return;
	}
    
    let script = document.createElement('script');
    script.src = 'https://apis.google.com/js/api.js?onload=loadPicker';
    document.head.appendChild(script);
    
	var code=getUrlParameter('code');
	let requestHeader = googleDrive.auth==='1'? {authorization: 'Bearer '+window.localStorage.getItem('swagger_accessToken')} : null; 
	var loopback=new Loopback(googleDrive.root,googleDrive.base);
	
	
	var callback=location.origin+location.pathname;
    
	var methodAction=googleDrive.getAction.split(':');
	processing(true);
	loopback._request(methodAction[1],methodAction[0],{callback:callback},requestHeader,function(err,result){
		processing(false);
		if (err)
			return;
		$('#'+googleDrive.buttonId).click(function(){
            location.href=result.location;			
		});
        
        googleDrive.access=result;
	});

    if (code) {
        methodAction=googleDrive.postAction.split(':');
        loopback._request(methodAction[1],methodAction[0],{code:code, callback:callback},requestHeader,function(err,result){
            processing(false);
            if (err)
                return;
            
            notify('Access granted','success');
            setTimeout(function(){
                location.href=callback;
            },500);
            
        });
        
    }
	
}

$(document).ready(drive);