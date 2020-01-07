<?php

/*
 * (c) YOUR NAME <your@email.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

// If you don't to add a custom vendor folder, then use the simple class
// namespace HelloComposer;
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

        $path = '';
        $query = '';

        switch ($request['type']) {
            case 'query':
                $path = 'queries/'.$request['file'].'.graphql';
                break;
            case 'mutation':
                $path = 'mutations/'.$request['file'].'.graphql';
                break;
        }
        
        $query = file_get_contents($path, true);

        $_params = $request['params'];
        $_input = '';

        foreach ($_params as $key => $item) {
            $_input .= $key.' '.$item." \n";
        }

        $query = sprintf($query, $_input);

        $this->log[] = $query;
        // die();

        if ($request['type'] == "mutation") {
            return null;
        }
        
        if (empty($query)) {
            return null;
        }

        $result = $this->Exec($query);
        
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
