<?php
namespace Client;

class MongoData {
    public $order_by = [
                'ASC',
                'DESC'
    ];

    public $order_values = [
                'ASC'=>1,
                'DESC'=>-1
    ];

    public $conditions = [
                '=',
                '>',
                '<',
                '<>',
                '>=',
                '<='
    ];

    public $errors = [];

    public function getOptions($order = [], $skip = [], $limit = [])
    {
        $options = [];
        if (sizeof($order) == 2 && in_array($order[1], $this->order_by)) {
            $options['sort'] = [$order[0]=>$this->order_values[$order[1]]];
        }
        if (sizeof($skip) > 0 && is_numeric($skip[0])) {
            $options['skip'] = intval($skip[0]);
        }
        if (sizeof($limit) > 0 && is_numeric($limit[0])) {
            $options['limit'] = intval($limit[0]);
        }

        return $options;
    }

    public function getFilterOptions($where = [])
    {
        $data = [];
        if (sizeof($where) > 0) {
            foreach ($where as $key => $and) {
                if (sizeof($and) > 0) {
                    foreach ($and as $key => $or) {
                        if(sizeof($or) != 3 || !in_array($or[1], $this->conditions)) {
                            $this->errors[] = 'Not allowed WHERE condition';
                            break;
                        }
                    }
                } else {
                    $this->errors[] = 'Not allowed WHERE condition';
                    break;
                }
            }
        }

        if (sizeof($this->errors) == 0) {
            foreach ($where as $key => $or) {
                if (sizeof($or) > 1) {
                    $or_and = [];
                    foreach ($or as $key => $and) {
                        $or_and['$and'][] = $this->formatFilterArray($and);
                    }

                    $data['$or'][] = $or_and;
                } else {
                    $data['$or'][] = $this->formatFilterArray($or[0]);
                }
            }
        }

        return $data;

    }

    public function formatFilterArray($data)
    {
        if (sizeof($data) == 3) {
            switch ($data[1]) {
                case '=':
                    return $formatted = [$data[0]=>$data[2]];

                case '>':
                    return $formatted = [$data[0]=>['$gt'=>$data[2]]];

                case '<':
                    return $formatted = [$data[0]=>['$lt'=>$data[2]]];

                case '<>':
                    return $formatted = [$data[0]=>['$ne'=>$data[2]]];

                case '>=':
                    return $formatted = [$data[0]=>['$gte'=>$data[2]]];

                case '<=':
                    return $formatted = [$data[0]=>['$lte'=>$data[2]]];
            }

        }

        return [];
    }

    public function getSelectValues($select = [])
    {
        if (sizeof($select) > 0) {
            $data = [];
            foreach ($select as $key => $value) {
                if (strpos($value, '.') !== false && substr_count($value, '.') == 1) {
                    $data[] = $substring = explode('.', $value);
                } else {
                    $data[] = [$value];
                }
            }
        } else {
            $data = [['*']];
        }
        return $data;
    }

}