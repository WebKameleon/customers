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
                
                $kk=$m['summary']?' - '.$m['summary']:'';
                $loopbackOptions.='<option '.$s.' value="'.$value.'">'.strtoupper($mName).' '.$k.$kk.'</option>';
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
    
    function swaggerSummary($swagger,$loopback) {
        return swagger($swagger,isset($loopback['action'])?$loopback['action']:null,isset($loopback['parameters'])?$loopback['parameters']:[],isset($loopback['fields'])?$loopback['fields']:[]);
    }
    
    function relations($root, $swagger, $action) {
        if (!$action)
            return [];
        
        
        $action=explode(':',$action);
        if (count($action)!=2)
            return [];
        
        $path = $swagger['paths'][$action[1]][$action[0]];
        if (!$path)
            return [];
        
        
        
        $response = explode('/',$path['responses'][200]['schema']['items']['$ref']?:$path['responses'][200]['schema']['$ref']);
        $class=$response['2'];
        
        $url=$root.substr($swagger['basePath'],1).$action[1].'/show-relations';
        
        $relations=@json_decode(file_get_contents($url),true);
        
        if (!$relations) {
            $url=$root.substr($swagger['basePath'],1).dirname($action[1]).'/show-relations';
            $relations=@json_decode(file_get_contents($url),true);
        }
        
        if (!$relations) {
            $url=$root.substr($swagger['basePath'],1).'/'.strtolower(preg_replace('/([a-z])([A-Z])/',"\\1-\\2",$class)).'/show-relations';
            $relations=@json_decode(file_get_contents($url),true);
        }
   
        if (count($relations)) {
            foreach($relations AS $k=>$r) {
                if (!$swagger['definitions'][$r['model']]['properties'])
                    continue;
                
                $relations[$k]['fields'] = [];
                foreach ($swagger['definitions'][$r['model']]['properties'] AS $name=>$f) {
                    $relations[$k]['fields'][$name]['name']=$name;
                    $relations[$k]['fields'][$name]['type']=$f['type'];
                    if (isset($f['format']))
                        $relations[$k]['fields'][$name]['type'] = $f['format'];
                }
            }
        }
     
        return $relations;
        
    }
    
    function getRelations($loopbackRoot, $swagger, &$loopback) {
        if ($loopback['action']) {
            $fields=$loopback['fields'];
            $include=[];
            $relations=relations($loopbackRoot, $swagger, $loopback['action']);
            if ($relations && $fields) {
                foreach ($relations AS $k=>$r)
                    $include[]=$k;
                
                if ($loopback['include'] && strlen($loopback['include'])) {
                    
                    foreach(explode(',',$loopback['include']) AS $inc) {
                        if (!$relations[$inc]['fields'])
                            continue;
                    
                        foreach ($relations[$inc]['fields'] AS $f=>$field) {
                            $name=$inc.'.'.$f;
                            $fields[$name]['name'] = $inc.':'.$field['name'];
                            $fields[$name]['type'] = $field['type'];
                        }
                    }
                }
                foreach ($relations AS $k=>$r) {
                    if ($r['type']=='hasMany') {
                        $fields[$k.'Count']['name'] = $k.'Count';
                        $fields[$k.'Count']['type'] = 'double';
                        $fields[$k.'Count']['relation'] = $k;
                    }
                }
            }
            $loopback['includes'] = $include;
            $loopback['fields'] = $fields;
            $loopback['relations'] = $relations;
        }
    }