<?php
    use PHPUnit\Framework\TestCase;

    class MongoDataTest extends TestCase {

        public function testGetOptions()
        {
            $mongodata = new \Client\MongoData();
            $order = [];
            $skip = [];
            $limit = [];
            $options = $mongodata->getOptions($order, $skip, $limit);
            $this->assertInternalType('array', $options);
            $this->assertEmpty($options);

            $order = ['first_name','ASC'];
            $options = $mongodata->getOptions($order, $skip, $limit);
            $this->assertInternalType('array', $options);
            $this->assertEquals(1, $options['sort']['first_name']);

            $skip = [10];
            $options = $mongodata->getOptions($order, $skip, $limit);
            $this->assertInternalType('array', $options);
            $this->assertEquals(10, $options['skip']);

            $limit = [5];
            $options = $mongodata->getOptions($order, $limit, $limit);
            $this->assertInternalType('array', $options);
            $this->assertEquals(5, $options['limit']);
        }

        public function testGetFilterOptions()
        {
            $mongodata = new \Client\MongoData();
            $where = [];
            $data = $mongodata->getFilterOptions($where);
            $this->assertInternalType('array', $data);
            $this->assertEquals([], $data);

            $where = [[['a','=','b']]];
            $data = $mongodata->getFilterOptions($where);
            $this->assertInternalType('array', $data);
            $this->assertInternalType('array', $data['$or']);
        }

        public function testFormatFilterArray()
        {
            $mongodata = new \Client\MongoData();
            $data = [];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals([],$formatted);

            $data = ['a','=','b'];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals(['a'=>'b'], $formatted);

            $data = ['a','>','b'];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals(['a'=>['$gt'=>'b']], $formatted);

            $data = ['a','<','b'];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals(['a'=>['$lt'=>'b']], $formatted);

            $data = ['a','<>','b'];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals(['a'=>['$ne'=>'b']], $formatted);

            $data = ['a','>=','b'];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals(['a'=>['$gte'=>'b']], $formatted);

            $data = ['a','<=','b'];
            $formatted = $mongodata->formatFilterArray($data);
            $this->assertEquals(['a'=>['$lte'=>'b']], $formatted);
        }

        public function testGetSelectValues()
        {
            $mongodata = new \Client\MongoData();
            $select = [];
            $data = $mongodata->getSelectValues($select);
            $this->assertInternalType('array', $data);
            $this->assertEquals('*', $data[0][0]);

            $select = ['*','tags.0'];
            $data = $mongodata->getSelectValues($select);
            $this->assertInternalType('array', $data);
            $this->assertEquals('*', $data[0][0]);
            $this->assertEquals('tags', $data[1][0]);
        }
    }
?>
