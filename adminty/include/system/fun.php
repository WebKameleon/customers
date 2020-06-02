<?php


    function loopbackRootUrl($webpage,$url=false) {
        
        if ($url) {
            $wpm=new webpageModel($webpage['sid']);
            $wpm->pagekey=$url;
            $wpm->save();
            return $url;
            
        }
        
        if ($webpage['pagekey'])
            return $webpage['pagekey'];
        
        
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
                    
                    if (isset($m['responses']) && isset($m['responses']['200']) && isset($m['responses']['200']['schema']) && isset($m['responses']['200']['schema']['items']) && isset($m['responses']['200']['schema']['items']['$ref'])) {
                        $ref=explode('/',$m['responses']['200']['schema']['items']['$ref']);
                        
                        foreach ($swagger[$ref[1]][$ref[2]]['properties'] AS $name=>$f) {
                            $fields[$name]['name']=$name;
                            $fields[$name]['type']=$f['type'];
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