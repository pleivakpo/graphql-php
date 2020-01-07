# Graphql Php
Composer library for consume GraphQL from Php with Curl, by Kappo Bike.

## Install by composer

```bash
    composer require kappo/graphql-php
```

## Query Example

Create this path with the file getMaster.graphql
```path
/inc/queries/getMaster.graphql
```
```graphql
    query {  
        listMaster %s {
            items{
                name
                createdAt
            }
        }
    }
```

Create a class on Master.php extends from Kappo\Graphql
```php

use Kappo\Graphql;

class Master extends Graphql
{
    
    public function __construct(){
        parent::__construct("[YOUR_ENDPOINT]","[YOUR_APYKEY]");
    }

    public function get(){
        
        $result = $this->gql_exec(array(
            'type' => 'query'
            ,'file' => file_get_contents(__DIR__.'/inc/queries/getMaster.graphql',true)
            ,'params' => array(
                'filter' => array(
                    "reference:" => "{eq:".$this->String("app")."}",
                    "stage:"     => "{eq:".$this->String("qa")."}",
                    "isActive:"  => "{eq:".$this->String("Yes")."}"
                )
            )
        ));

        print_r($result);

    }
}

$master = new Master();
$master->Get();

```

Test php file with

```bash
    php Master.php
```