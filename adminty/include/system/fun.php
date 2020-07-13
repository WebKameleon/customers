<?php

    function loopbackRootUrlFinal($url) {
        $mode=Bootstrap::$main->session('editmode');
        $url=explode(',',$url);
        while (!isset($url[$mode]))
            $mode--;
        return $url[$mode];
    }

    function loopbackRootUrl($webpage,$url=false) {
        
        if ($url) {
            $wpm=new webpageModel($webpage['sid']);
            $wpm->pagekey=$url;
            $wpm->save();
            return loopbackRootUrlFinal($url);
        }
        
        
        if ($webpage['pagekey']) {
            return loopbackRootUrlFinal($webpage['pagekey']);
        }
        
        
        if ($webpage['prev']!=-1 && strlen($webpage['prev'])) {
            $wpm=new webpageModel();
            $page=$wpm->getOne($webpage['prev']);
            return loopbackRootUrl($page);
        }
        
        return '';
    }
    
    
    function swagger($swagger,$action,$parameters,$fields) {
        $loopbackOptions='';
        $lastGroup='';
        
        foreach ($swagger['paths'] AS $k=>$p) {
            
            $patha=explode('/',$k);
            if ($lastGroup!=$patha[1]) {
                $lastGroup=$patha[1];
                $loopbackOptions.='<optgroup label="'.$lastGroup.'">';
            }
            
            $p2=str_replace('/'.$lastGroup.'/','',$k);
            foreach($p AS $mName => $m) {
                $value=$mName.':'.$k;
                $s=($action==$value)?'selected':'';
                $loopbackOptions.='<option '.$s.' value="'.$value.'">'.strtoupper($mName).' '.$k.'</option>';
                if (strlen($s)) {
                    
                    
                    foreach ($m['parameters'] AS $mp) {
                        
                        if (isset($mp['schema']) && isset($mp['schema']['$ref'])) {
                            $ref=explode('/',$mp['schema']['$ref']);
                            foreach ($swagger[$ref[1]][$ref[2]]['properties'] AS $name=>$f) {
                                if ($name=='id')
                                    continue;
                                $parameters[$name]['name']=$name;
                                $parameters[$name]['type']=$f['type'];
                            }
                            continue;
                        }
                        if ($mp['name']=='id')
                            continue;
                        $parameters[$mp['name']]['name'] = $mp['name'];
                        $parameters[$mp['name']]['type'] = $mp['type'];
                        if (isset($parameters[$mp['name']])) {
                            if ($mp['required'] && !isset($parameters[$mp['name']]['require'])) {
                                $parameters[$mp['name']]['require'] = '';
                            }
                            continue;
                        }
                        $parameters[$mp['name']]['label'] = '';
                        if ($mp['required'])
                            $parameters[$mp['name']]['require'] = '';
                    }
                    
                    $ref=null;
                    if (isset($m['responses']) && isset($m['responses']['200']) && isset($m['responses']['200']['schema']) && isset($m['responses']['200']['schema']['items']) && isset($m['responses']['200']['schema']['items']['$ref']))
                        $ref=explode('/',$m['responses']['200']['schema']['items']['$ref']);
                    if (isset($m['responses']) && isset($m['responses']['200']) && isset($m['responses']['200']['schema']) && isset($m['responses']['200']['schema']['$ref']))
                        $ref=explode('/',$m['responses']['200']['schema']['$ref']);
                    
                    
                    if ($ref) {
                        
                        foreach ($swagger[$ref[1]][$ref[2]]['properties'] AS $name=>$f) {
                            $fields[$name]['name']=$name;
                            $fields[$name]['type']=$f['type'];
                            if (isset($f['format']))
                                $fields[$name]['type'] = $f['format'];
                        }
                           
                    }
                    
                    
                    
                    
                    
                }
            }
            
        }
        
        return [
            'parameters' => $parameters,
            'loopbackOptions' => $loopbackOptions,
            'fields' => $fields
        ];
        
    }