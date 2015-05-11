<?php

$shoper_url='http://wbp.shoparena.pl/webapi/json/';


function login($c, $login, $password) {
    $params = Array(
        "method" => "login",
        "params" => Array($login, $password)
    );
    curl_setopt($c, CURLOPT_POSTFIELDS, "json=" . json_encode($params));
    $result = (Array) json_decode(curl_exec($c));
    if (isset($result['error'])) {
        return null;
    } else {
        return $result[0];
    }
}    

function getError($c, $session){
    $params = Array(
        "method" => "call",
        "params" => Array($session, 'internals.validation.errors', null)
    );
    curl_setopt($c, CURLOPT_POSTFIELDS, "json=" . json_encode($params));
    $result = (Array) json_decode(curl_exec($c));
    return $result;
}
 
$c = curl_init();
curl_setopt($c, CURLOPT_URL, $shoper_url);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

// zalogowanie użytkownika i pobranie identyfikatora sesji
$session = login($c, "webkameleon", "Kameleon2014");


$categories=array();
$catWbp2shoper=array();
$prodWbp2shoper=array();


 
if ($session != null) {
    $params = Array(
        "method" => "call",
        "params" => Array($session, "category.list", 
                Array(true, true,null)
            )
    );
 
    // zakodowanie parametrów dla metody POST
    $postParams = "json=" . json_encode($params);
    curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);
 
    // dekodowanie rezultatu w formacie JSON do tablicy result
    $data = curl_exec($c);
    $result = json_decode($data,1);
 
    // sprawdzenie, czy wystąpił błąd
    if (isset($result['error'])) {
        echo "Wystąpił błąd: " . $result['error'] . ", kod: " . $result['code'];
    } else {
        foreach ($result AS $cat)
        {
            $categories[$cat['translations']['pl_PL']['name']] = $cat['category_id'];
        }
        
        $sql="SELECT * FROM wbp_TowaryKategorie";
        
        $q=$src->query($sql);
        if ($q) foreach ($q AS $row ){
        
            if (!isset($categories[$row['TowaryKategorieNazwa']]))
            {
               
                $category = Array(        
                    "parent_id" => 0,
                    "order" => count($categories)+1,
                    "translations" => Array(
                        "pl_PL" => Array(
                            "name" => $row['TowaryKategorieNazwa'],
                            "description" => "",
                            "active" => 1,
                            "seo_title" => "",
                            "seo_description" => "",
                            "seo_keywords" => "",
                        ),
                    ),
                );
                 
                $params = Array(
                    "method" => "call",
                    "params" => Array($session, "category.create", Array($category))
                );
             
                // zakodowanie parametrów dla metody POST
                $postParams = "json=" . json_encode($params);
                curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);               
               
                $data = curl_exec($c);
                $result = json_decode($data,1);
                
                if (isset($result['error'])) {
                    echo "Wystąpił błąd: " . $result['error'] . ", kod: " . $result['code'];
                } else {
                    $categories[$row['TowaryKategorieNazwa']]=$result;
                    $catWbp2shoper[$row['TowaryKategorieId']] = $categories[$row['TowaryKategorieNazwa']];
                }
               
                
            }
            else {
                $catWbp2shoper[$row['TowaryKategorieId']] = $categories[$row['TowaryKategorieNazwa']];
            }

        }
        
        
        $params = Array(
            "method" => "call",
            "params" => Array($session, "product.list", 
                    //Array(true, false, false,false,false, null),
                    Array(true, true, true,true,true, null),
                )
        );
     
        // zakodowanie parametrów dla metody POST
        $postParams = "json=" . json_encode($params);
        curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);
     
        // dekodowanie rezultatu w formacie JSON do tablicy result
        $data = curl_exec($c);
        $result = json_decode($data,1);        
        
        foreach ($result AS $prod)
        {
            $token=$prod['code'];
            if (strstr($prod['code'],'ISSN')) $token.=':'.md5($prod['translations']['pl_PL']['name']); 
            $prodWbp2shoper[$token] = $prod['product_id'];
        }

        
        $sql="SELECT * FROM wbp_Towary WHERE 1=1";
        if ($wbp_id) $sql.=" AND TowaryId=$wbp_id";
        if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
        if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
        if ($wbp_like) $sql.=" AND (TowaryIsbn LIKE '%$wbp_like%' OR TowaryTytul LIKE '%$wbp_like%')";
        
        
        
        $i=0;
        $q=$src->query($sql);
        if ($q) foreach ($q AS $row ){
            $i++;

        
            $code=strtoupper($row['TowaryIsbn']);
            if (!$code) $code=$row['TowaryId'];
            $code=str_replace(' ','',$code);
            $code=str_replace('_','',$code);
            $code=str_replace(':','',$code);
            $code=str_replace('IISBN','ISBN',$code);
            $code=str_replace('IISSN','ISSN',$code);
            $code=str_replace('ISBN','ISBN ',$code);
            $code=str_replace('ISSN','ISSN ',$code);
            
            $orig_code=$code;
            if (strstr($code,'ISSN'))
            {
                $pat=array();
                if ($pos=strpos($row['TowaryTytul'],' Nr ')) $code.=substr($row['TowaryTytul'],$pos);
                elseif (preg_match('~[0-9]+\/20[0-9][0-9]~',$row['TowaryTytul'],$pat)) $code.=' '.$pat[0];
                elseif (preg_match('~20[0-9][0-9]~',$row['TowaryInfo'],$pat)) $code.=' '.$pat[0];
            }

            
            $name=trim($row['TowaryAutor']);
            if($name && trim($row['TowaryTytul'])) $name.=' - ';
            $name.=trim($row['TowaryTytul']);
            
            $token=$code;
            
            
            if (!isset($prodWbp2shoper[$token]))
            {
                $product = Array(        
                    "producer_id" => 39,
                    "tax_id" => 1,
                    "category_id" => $catWbp2shoper[$row['Towary_KategoriaId']],
                    "unit_id" => 2,
                    "code" => $code,
                    "pkwiu" => null,
                    "stock" => Array(
                        "price" => 10,
                        "stock" => 10,
                        "warn_level" => 2,
                        "sold" => 0,
                        "weight" => 0.5,
                        "availability_id" => null,
                        "delivery_id" => null,
                        "gfx_id" => null,
                    ),
                    "translations" => Array(
                        "pl_PL" => Array(
                            "name" => $name,
                            "short_description" => "",
                            "description" => "",
                            "active" => 1,
                            "seo_title" => "",
                            "seo_description" => "",
                            "seo_keywords" => "",
                            "order" => null,
                            "main_page" => 0,
                            "main_page_order" => null,
                        ),
                    ),
                );
                $params = Array(
                    "method" => "call",
                    "params" => Array($session, "product.create", Array($product))
                );
             
                // zakodowanie parametrów dla metody POST
                $postParams = "json=" . json_encode($params);
                curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);
             
                // dekodowanie rezultatu w formacie JSON do tablicy result
                $data = curl_exec($c);
                $result = json_decode($data,1);                    
  
  
                if (isset($result['error'])) {
                    echo "Wystąpił błąd: " . $result['error'] . ", kod: " . $result['code'];
                } else {
                    if ($result[0] == -1) {
                    /*
                        echo "Podane dane są nieprawidłowe i nie spełniają wymagań walidacji";
                        $err = getError($c, $session);
                            foreach($err as $error){
                                echo PHP_EOL.$error;
                            }
                    */
                    } else if ($result[0] == 0) {
                    /*
                        echo "Operacja się nie udała";
                        $err = getError($c, $session);
                            foreach($err as $error){
                                echo PHP_EOL.$error;
                            }
                    */
                    }
                }             

  
  
                if (!is_array($result) && $result>0) $prodWbp2shoper[$token]=$result;
      
            }
            
            if (!isset($prodWbp2shoper[$token])) continue;
        
        
            if ($row['TowaryGrafika'])
            {
                $url='http://www.wbp.poznan.pl/files/towary/large/'.$row['TowaryGrafika'];  
                $image = Array(        
                    "file" => $row['TowaryGrafika'],
                    "content" => null, // można użyć zdjęcia zakodowanego base64
                    "url" => $url, 
                    "name" => $row['TowaryTytul'],
                );
                 
                $params = Array(
                    "method" => "call",
                    "params" => Array($session, "product.image.save", Array($prodWbp2shoper[$token], $image, true)) 
                        // id produktu, zdjęcie, force
                );
             
                $postParams = "json=" . json_encode($params);
                curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);
             
                // dekodowanie rezultatu w formacie JSON do tablicy result
                $data = curl_exec($c);
                $result = json_decode($data,true);
             
                if (isset($result['error'])) {
                    echo "Wystąpił błąd: " . $result['error'] . ", kod: " . $result['code'];
                } else {
                    if ($result[0] == -1) {
                    /*
                        echo "Podane dane są nieprawidłowe i nie spełniają wymagań walidacji";
                        $err = getError($c, $session);
                            foreach($err as $error){
                                echo PHP_EOL.$error;
                            }
                    */
                    } else if ($result[0] == 0) {
                    /*
                        echo "Operacja się nie udała";
                        $err = getError($c, $session);
                            foreach($err as $error){
                                echo PHP_EOL.$error;
                            }
                    */
                    } else {
                        //echo "Id dodanego zdjęcia: " . $result[0];
                    }
                }             
             
             
                             
            
            }
        

            $rok='';
            $stron='';
            $oprawa='';
            $format='';
            
            $info=$row['TowaryInfo'];
            $info=str_replace(',5 ','.5 ',$info);
            $info=explode(',',$info);
            
            foreach ($info AS $inf)
            {
                if (strstr($inf,'format')) $format=trim(str_replace('format','',$inf));
                if (strstr($inf,'rok')) $rok=trim(preg_replace('/[^0-9]/','',$inf));
                if (strstr($inf,'str')) $stron=trim(preg_replace('/[^0-9]/','',$inf));
                if (strstr($inf,'oprawa')) $oprawa=trim(str_replace('oprawa','',$inf));        
            }
            
            $row['TowaryOpisHtml']=preg_replace('~<img[^>]*>~i','',$row['TowaryOpisHtml']);
            
            $attr=array (
                        167 => $row['TowaryAutor'],
                        171 => $rok,
                        169 => $stron,
                        168 => $format,
                        170 => $oprawa
            );
            if (strstr($code,'ISSN'))
                $attr=array (
                        174 => $row['TowaryTytul'],
                        175 =>'',
                        179 => $rok,
                        178 => $stron,
                        176 => $format,
                        177 => $oprawa
                );
            
            $product = Array(        
                "producer_id" => 39,
                "tax_id" => 1,
                "category_id" => $catWbp2shoper[$row['Towary_KategoriaId']],
                "unit_id" => 2,
                "code" => $code,
                "pkwiu" => null,
                "stock" => Array(
                    "price" => $row['TowaryCena'],
                    "stock" => 10,
                    "warn_level" => 2,
                    "sold" => 0,
                    "weight" => 0.5,
                    "availability_id" => null,
                    "delivery_id" => 2, //http://wbp.shoparena.pl/admin/configDeliveries
                    "gfx_id" => null,
                ),
                "translations" => Array(
                    "pl_PL" => Array(
                        "name" => $name,
                        "short_description" => "",
                        "description" => $row['TowaryOpisHtml'],
                        "active" => $row['TowaryStatus'],
                        "seo_title" => "",
                        "seo_description" => "",
                        "seo_keywords" => $row['TowaryKlucz'],
                        "order" => null,
                        "main_page" => 0,
                        "main_page_order" => null,
                    ),
                ),
                "attributes" => $attr
            );
             
            //print_r($product);
            $params = Array(
                "method" => "call",
                "params" => Array($session, "product.save", Array($prodWbp2shoper[$token], $product, true)) 
            );
         
            $postParams = "json=" . json_encode($params);
            curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);
         
            // dekodowanie rezultatu w formacie JSON do tablicy result
            $data = curl_exec($c);
            $result = json_decode($data,1);


            if (isset($result['error'])) {
                echo "Wystąpił błąd: " . $result['error'] . ", kod: " . $result['code'];
            } else {
                if ($result[0] == -1) {
                    echo "Podane dane są nieprawidłowe i nie spełniają wymagań walidacji";
                    $err = getError($c, $session);
                        foreach($err as $error){
                            echo PHP_EOL.$error;
                        }
                } else if ($result[0] == 0) {
                    /*
                    echo "Operacja się nie udała";
                    $err = getError($c, $session);
                        foreach($err as $error){
                            echo PHP_EOL.$error;
                        }
                    */
                } else if ($result[0] == 1) {
                    //echo "Produkt został zapisany";
                } else if ($result[0] == 2) {
                    //echo "Operacja się nie udała - obiekt jest zablokowany przez innego administratora";
                }
            }
            
            
            echo "$i.   $code: $name\n";
            //print_r($row);
        }    
        
        
        
        //print_r($categories);
    }
} else {
    echo "Wystąpił błąd logowania";
}
 
curl_close($c);


    $sql="wbp_TowaryKategorie";


    
    