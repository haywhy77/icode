<?php
function sanitizeArray(array $record, $exclude){
    
    if(!is_array($exclude) || empty($exclude) || count($exclude)<1 ) return $record;
    
    return array_filter($record, function($k) use ($exclude) {return !in_array($k, $exclude);}, ARRAY_FILTER_USE_KEY);
}
function array2HTML(array $headers, array $content, $exclude=null){
    $width = 'width="100%"';
    $table = '<table align="center" '.$width.' bgcolor="#FFFFFF" border="1px solid" style="border-collapse: collapse">';
    $rows = array();
    if($headers):
        $header='<tr><th>SN</th>';
        foreach ($headers as $key => $value):
            $header .= "<th><strong>{$value}</strong></th>";
        endforeach;
        $header.='</tr>';
    endif;

    $rows[]=$header;
    
    foreach ($content as $key => $value):
        // $value=sanitizeArray($value, $exclude);
        
        $value=sanitizeArray($value, $exclude);
        // var_dump($value);exit;
        $keys=array_keys($value);
        
        $count=0;
        $row="<tr><td>".($key+1)."</td>";
        while($count<count($keys)){
            
            $value_type = gettype($value[$keys[$count]]);
            switch ($value_type) {
                case 'string':
                    $val = (in_array($value[$keys[$count]], array(""))) ? "&nbsp;" : $value[$keys[$count]];
                    $row .= "<td>{$val}</td>";
                    break;
                case 'integer':
                    $val = (in_array($value[$keys[$count]], array(""))) ? "&nbsp;" : $value[$keys[$count]];
                    $row .= "<td>{$value[$keys[$count]]}</td>";
                    break;
                // case 'array':
                //     if (gettype($key) == "integer"):
                //         $row .= array2HTML($value[$count], false);
                //     elseif(gettype($key) == "string"):
                //         $row .= "<tr><td><strong>{$key}</strong></td><td>".
                //         array2HTML($value[$count], true, true) . "</td>";
                //     endif;
                //     break;
                default:
                    # code...
                    break;
            }
            ++$count;
        }
        $row.="</tr>";
        $rows[] = $row;
    endforeach;
    $ROWS = implode("\n", $rows);
    $table .=  $ROWS . '</table>';
    return $table;
}