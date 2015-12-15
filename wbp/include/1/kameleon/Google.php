<?php

class Google {
    
    const TOKEN_ENDPOINT = 'https://accounts.google.com/o/oauth2/token';
    const AUTH_ENDPOINT = 'https://accounts.google.com/o/oauth2/auth';
    
    const CLIENT_ID = "243294196991.apps.googleusercontent.com";
    const CLIENT_SECRET = "TryrcvBXGoZyywb6bu4Zbss7";
    

    protected static $token=null;

    public static function getAccessToken($scope,$callback=null)
    {
	if (is_null($callback)) $callback='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	
	if (isset($_GET['code']))
	{
	    
		$post=array(
		    'code'=>$_GET['code'],
		    'redirect_uri'=>$callback,
		    'client_id'=>self::CLIENT_ID,
		    'client_secret'=>self::CLIENT_SECRET,
		    'grant_type'=>'authorization_code',
		);
		
                $t=self::request(self::TOKEN_ENDPOINT,'POST',$post);
	    
	    	if (isset($t->expires_in)) $t->expire=$t->expires_in+time();
		return $t;
	}
	else
	{
	    $url=self::AUTH_ENDPOINT.'?redirect_uri='.urlencode($callback).'&response_type=code&client_id='.$clientID.'&approval_prompt=force&scope='.urlencode($scope).'&access_type=offline'; 

	    Header('Location: '.$url);
	    die("<a href='$url'>$url</a>");
	}
	
    }


    protected static function refreshAccessToken($token)
    {
		
		$post=array(
			'refresh_token'=>$token->refresh_token,
			'client_id'=>self::CLIENT_ID,
			'client_secret'=>self::CLIENT_SECRET,
			'grant_type'=>'refresh_token',
		);
	
	
		$t=self::request(self::TOKEN_ENDPOINT,'POST',$post,'','json-obj');

			
		
		if (isset($t->expires_in))
		{
			$t->expire=$t->expires_in+time();
			$t->created=time();
		}
		return $t;
	
    }    
    
    public static function req($url)
    {
        return self::request($url);
    }
  
    
    protected static function request($url,$method='GET',$data=null,$scope_required='',$return_kind='',$user=null, $header=array()) {
        $ch = curl_init();
        

		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        
        $h=array();
        if (is_object(self::$token))
        {
            $h[]='Authorization: '.self::$token->token_type.' '.self::$token->access_token;
        }
    
        foreach ($header AS $k=>$v)
        {
            if (is_integer($k) && strpos($k,':')) $h[]=$v;
            else $h[]="$k: $v";
        }
        
        if ($method=='POST' || $method=='PUT') {
            if ($return_kind=='json') $data=json_encode($data);
        
        
            if ($method=='PUT') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            else curl_setopt($ch, CURLOPT_POST,   1);

            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

            if ($return_kind=='xml') $h[]='Content-Type: application/atom+xml; charset=UTF-8';
            if ($return_kind=='json') $h[]='Content-Type: application/json; charset=UTF-8';
        }

		
        if (count($h)) curl_setopt($ch,CURLOPT_HTTPHEADER,$h);
		
		if ($return_kind=='header')
		{
			//curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
		}
		
        $ret = curl_exec($ch);
		
        curl_close($ch);
        
        //if ($method=='POST' || strstr($url,'cell') ) mydie(simplexml_load_string($ret),$url);

        if ($return_kind=='xml') return simplexml_load_string($ret);
        if ($return_kind=='json') return json_decode($ret,true);
        if ($return_kind=='json-obj') return json_decode($ret);
        if ($return_kind=='header') {
			$a=[];
			foreach(explode("\n",$ret) AS $line) {
				$pos=strpos($line,':');
				if ($pos) $a[substr($line,0,$pos)]=trim(substr($line,$pos+1));
			}
			$ret=$a;
		}
        
        
        return $ret;        
    }
    
    public static function setToken($token)
    {
	
        if (is_object($token) && isset($token->created) && $token->created + $token->expires_in < time())
        {
            $token=self::refreshAccessToken($token);
        }   
        self::$token=$token;
        
        return $token;
    }
    
    
    
    
    public static function getFile($fileId)
    {
        $url='https://www.googleapis.com/drive/v2/files/'.$fileId;
        
        return self::request($url,'GET',null,'','json');
    }
    
    public static function getFileChildren($fileId,$title='')
    {
        $url='https://www.googleapis.com/drive/v2/files/'.$fileId.'/children';
        if ($title) $url.='?q='.urlencode('title="'.$title.'"');
        
        
        return self::request($url,'GET',null,'','json');
    }

    public static function createFolder($title,$parent=null)
    {
        
        $metadata=array('title'=>$title,'mimeType'=>'application/vnd.google-apps.folder');
        if ($parent) $metadata['parents']=array(array('id'=>$parent));
                
        return self::request('https://www.googleapis.com/drive/v2/files','POST',$metadata,'','json');
    }

    
    
    public static function uploadFile($title,$type,&$data,$folder=null,$convert=false)
    {

        $boundary=md5(time());
		
		$chunk_size=4 * 1024 * 1024;
		$file_size=strlen($data);
	
		$metadata=array('title'=>$title,'mimeType'=>$type);
		if ($folder) $metadata['parents']=array(array('id'=>$folder));		
		
		if ($file_size < $chunk_size) {
        
			$url='https://www.googleapis.com/upload/drive/v2/files?uploadType=multipart&convert='.($convert?'true':'false');
			
			$header=array('Content-Type'=>'multipart/related; boundary="'.$boundary.'"');
			

			
			$body="--$boundary\nContent-Type: application/json; charset=UTF-8\n\n";
			$body.=json_encode($metadata);
			
			$body.="\n\n--$boundary\nContent-Type: $type\n\n";
			
			
			$post="\n--$boundary--";
			
	
		
			$ret=self::request($url,'POST',$body.$data.$post,'','',null,$header);
			
			return json_decode($ret,true);
		} else {
			
			$url='https://www.googleapis.com/upload/drive/v2/files?uploadType=resumable&convert='.($convert?'true':'false');
			
			$header=array(
				'Content-Type'=>'application/json; charset=UTF-8',
				'X-Upload-Content-Type'=> $type,
				'X-Upload-Content-Length'=> $file_size
			);
			
			
			
			$body=json_encode($metadata);
			$ret=self::request($url,'POST',$body, '','header',null,$header);
			
			
			
			if (isset($ret['X-GUploader-UploadID']))
			{
				$upload_id=$ret['X-GUploader-UploadID'];
			
				for ($i=0;$i<ceil($file_size/$chunk_size); $i++)
				{
					
					$blob_size=$i==ceil($file_size/$chunk_size)-1?$file_size-$i*$chunk_size:$chunk_size;
					$blob=substr($data,$i*$chunk_size,$blob_size);
					
					$url='https://www.googleapis.com/upload/drive/v2/files?uploadType=resumable&upload_id='.$upload_id;

					$header=array(
						'Content-Type'=>$type,
						'Content-Range'=> 'bytes '.($i*$chunk_size).'-'.($i*$chunk_size+$blob_size-1).'/'.$file_size,
						'Content-Length'=> $blob_size
					);
					
					$ret=self::request($url,'PUT',$blob, '','',null,$header);
					
					
				}
				return json_decode($ret,true);
			}
			
		}
		
    }
    
}
