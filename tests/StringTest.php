<?php
	use PHPUnit\Framework\TestCase;

	class StringTest extends TestCase {

		public function testCheckString() {
			$ostring = new \Client\String();
			$sql = '';
			$string = $ostring->checkString($sql);
			$this->assertFalse($string);
			$sql = 'SELECT * FROM table_name';
			$string = $ostring->checkString($sql);
			$this->assertTrue($string);
			$this->assertEquals($ostring->sql,$sql);
		}

		public function testNoErrors() {
			$errors = [];
			self::assertTrue(\Client\String::noErrors($errors));
			$errors = ['first_error','second_error'];
			self::assertFalse(\Client\String::noErrors($errors));
		}

		public function testCheckSringStructure() {
			$ostring = new \Client\String();
			$string = $ostring->checkSringStructure();
			$this->assertFalse($string);

			$ostring->sql = '';
			$string = $ostring->checkSringStructure();
			$this->assertFalse($string);

			$ostring->sql = 'SELECT';
			$string = $ostring->checkSringStructure();
			$this->assertFalse($string);
			$this->assertEquals('Invalid SQL',$ostring->errors[0]);

			$ostring->sql = 'SELECT * FROM table';
			$ostring->errors = [];
			$string = $ostring->checkSringStructure();
			$this->assertTrue(\Client\String::noErrors($ostring->errors));
			$this->assertEquals('*', $ostring->select[0]);
			$this->assertEquals('table', $ostring->from[0]);

			$ostring->sql = 'SELECT * FROM table WHERE id = 2 AND first_name = Andy ORDER BY first_name ASC SKIP 10 LIMIT 5';
			$ostring->errors = [];
			$string = $ostring->checkSringStructure();
			$this->assertTrue(\Client\String::noErrors($ostring->errors));
			$this->assertEquals('10', $ostring->skip[0]);
			$this->assertEquals('5', $ostring->limit[0]);
			$this->assertInternalType('array', $ostring->order);
			$this->assertEquals(2,sizeof($ostring->order));
			foreach ($ostring->order as $key => $value) {
				$this->assertEquals(1,sizeof($value));
			}

			$ostring->sql = 'SELECT * FROM table WHERE id = 2 AND first_name = Andy Andy ORDER BY first_name ASC SKIP 10 LIMIT 5';
			$ostring->errors = [];
			$string = $ostring->checkSringStructure();
			$this->assertEquals('Not allowed WHERE condition', $ostring->errors[0]);

			$ostring->sql = 'SELECT * FROM table WHERE id = 2 AND first_name = Andy ORDER BY first_name ASC SKIP 10 LIMIT 5 asd';
			$ostring->errors = [];
			$string = $ostring->checkSringStructure();
			$this->assertEquals('Not allowed LIMIT value', $ostring->errors[0]);

			$ostring->sql = 'SELECT * FROM table WHERE id = 2 AND first_name = Andy ORDER BY first_name ASC SKIP asd 10 LIMIT 5';
			$ostring->errors = [];
			$string = $ostring->checkSringStructure();
			$this->assertEquals('Not allowed SKIP value', $ostring->errors[0]);
		}

		public function testCheckPartStructure() {

		}
	}
?>
