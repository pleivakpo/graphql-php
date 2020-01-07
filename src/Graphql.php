<?php

namespace Kappo;

class Graphql
{
    public $log = [];    
    private $endPoint = "";
    private $apiKey = "";

    public function __construct($endPoint, $apiKey)
    {
        $this->endPoint = $endPoint;
        $this->apiKey = $apiKey;
    }
    

    public function String($srt)
    {
        $srt = $srt === '' || $srt === null ? 'null' : '"'.$srt.'"';

        return $srt;
    }

    public function Integer($int)
    {
        $int = $int === null ? 'null' : $int;

        return $int;
    }

    public function Enum($srt)
    {
        $srt = $srt === '' || $srt === null ? 'null' : $srt;

        return $srt;
    }

    public function Object($arr = array())
    {
        return json_decode(json_encode($arr), false);
    }
    
    public function gql_exec($request = array())
    {
        if (empty($request['type']) || empty($request['file'])) {
            die('Kappo GraphQL Exception :: gql_exec require a "type", "file" and "params" to execute Operation');
        }
        
        $_query = $request['file'];
                
        $_inputCollection = $request['params']['filter'];
        $_limit = isset($request['params']['limit']) ? $request['params']['limit'] : null;
        $_nextToken = isset($request['params']['nextToken']) ? $request['params']['nextToken'] : null;

        $_input='';

        $_inputstr = $request['type'] === 'query' ? 'filter' : 'input';

        if (count($_inputCollection)) {
            $_input = "$_inputstr:{\n";
            foreach ($_inputCollection as $key => $item) {
                $_input .= $key.' '.$item."\n";
            }
            $_input .='}';
        }

        if(!empty($_limit)){
            $_limit = "\n ,limit: ".$_limit;
        }

        if(!empty($_nextToken)){
            $_nextToken = "\n ,nextToken: ".$_nextToken;
        }

        $_content = "";

        if (isset($request['params'])) {
            $_format = "(%s %s %s)";
            $_content = sprintf($_format, $_input, $_limit, $_nextToken);
        }

        $query = sprintf($_query, $_content);

        $this->log[] = $query;
        
        if (empty($query)) {
            return null;
        }

        $result = null;//$this->Exec($query);
        
        return $result;
    }
    

    /**
     * @param query::String graphqlOpertaion
     */
    private function Exec($query)
    {
        $qry = json_encode(array( "query" => $query));
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'x-api-key: '.$this->apiKey;
        $ch = curl_init();
                
        curl_setopt($ch, CURLOPT_URL, $this->endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return null;
        }
        
        return json_decode($response);
    }
}
