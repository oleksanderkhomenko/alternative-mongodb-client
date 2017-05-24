<?php

require_once __DIR__ . '/vendor/autoload.php';

echo 'PLEASE ENTER DATABASE NAME: ';
$db = fgets(STDIN);

echo 'PLEAE ENTER SQL: ';
$sql = fgets(STDIN);

if(empty(trim($db)) || strpos($db, ' ') !== false || empty(trim($sql))) {
    echo 'No Databaase or no Sql';
    exit(0);
}

$string = new Client\StringSplitter();

if($string->checkString($sql)) {
    $string->checkSringStructure();

    if(Client\StringSplitter::noErrors($string->errors)) {
        $mongo = new Mongo();
        $database = trim($db);
        $table = $string->from[0];

        if(!empty($database) && !empty($table)) {
            $collection = $mongo->$database->$table;

            $mongodata = new \Client\MongoData();

            $select = $mongodata->getSelectValues($string->select);
            $where = $mongodata->getFilterOptions($string->where);
            $options = $mongodata->getOptions($string->order, $string->skip, $string->limit);
            $document = $collection->find($where);
            if (isset($options['limit'])) {
                $document = $document->limit($options['limit']);
            }
            if (isset($options['skip'])) {
                $document = $document->skip($options['skip']);
            }
            if (isset($options['sort'])) {
                $document = $document->sort($options['sort']);
            }

            foreach ($document as $current) {
                foreach ($select as $key => $value) {
                    if(sizeof($value) > 1) {
                        if(isset($current[$value[0]]) && isset($current[$value[0]][$value[1]])) {
                            print_r($current[$value[0]][$value[1]].' ');
                        }
                    } else if($value[0] == '*') {
                        print_r($current);
                    } else {
                        if(isset($current[$value[0]])) {
                            print_r($current[$value[0]].' ');
                        }
                    }
                }
            }
        }
    } else {
        print_r($string->errors[0]);
    }

} else {
    print_r($string->errors[0]);
}
exit(0);

