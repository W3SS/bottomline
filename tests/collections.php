<?php

class CollectionsTest extends \PHPUnit\Framework\TestCase
{
    // ...

    public function testEase()
    {
        $object = new \stdClass();
        // Arrange
        $a = ['foo' => ['bar' => 'ter'], 'baz' => ['b', 'z']];
        $b = ['foo' => ['bar' => $object], 'baz' => ['b', 'z']];

        // Act
        $x = __::ease($a);
        $y = __::ease($b);

        // Assert
        $this->assertEquals(3, count($x));
        $this->assertEquals(['foo.bar' => 'ter', 'baz.0' => 'b', 'baz.1' => 'z'], $x);
        $this->assertEquals(['foo.bar' => $object, 'baz.0' => 'b', 'baz.1' => 'z'], $y);
    }

    public function testFilter()
    {
        // Arrange
        $a = [1, 2, 3, 4, 5];
        $b = [
            ['name' => 'fred',   'age' => 32],
            ['name' => 'maciej', 'age' => 16]
        ];
        $c = [0, 1, false, 2, null, 3, true];

        // Act
        $x = __::filter($a, function($n) {
            return $n > 3;
        });
        $y = __::filter($b, function($n) {
            return $n['age'] == 16;
        });
        $z = __::filter($c);

        // Assert
        $this->assertEquals([4, 5], $x);
        $this->assertEquals([$b[1]], $y);
        $this->assertEquals([1, 2, 3, true], $z);
    }

    public function testFirst()
    {
        // Arrange
        $a = [1, 2, 3, 4, 5];

        // Act
        $x = __::first($a, 2);

        // Assert
        $this->assertEquals([1, 2], $x);
    }

    public function testGetArrays()
    {
        // Arrange
        $o = new \stdClass();
        $a = [
            'foo' => ['bar' => 'ter'],
            'baz' => ['foo' => ['obj' => $o]]
        ];

        // Act
        $x  = __::get($a, 'foo.bar');
        $x2 = __::get($a, 'foo.bar', 'default');
        $y  = __::get($a, 'foo.baz');
        $y2 = __::get($a, 'foo.baz', 'default');
        $y3 = __::get($a, 'foo.baz', function () {
            return 'default_from_callback';
        });
        $z = __::get($a, 'baz.foo.obj');

        // Assert
        $this->assertEquals('ter', $x);
        $this->assertEquals('ter', $x2);
        $this->assertNull($y);
        $this->assertEquals('default', $y2);
        $this->assertEquals('default_from_callback', $y3);
        $this->assertEquals($o, $z);
    }

    public function testGetObjects()
    {
        // Arrange
        $o = new \stdClass();
        $a = new \stdClass();
        $a->foo = new \stdClass();
        $a->foo->bar = 'ter';
        $a->baz = new \stdClass();
        $a->baz->foo = new \stdClass();
        $a->baz->foo->obj = $o;

        // Act
        $x  = __::get($a, 'foo.bar');
        $x2 = __::get($a, 'foo.bar', 'default');
        $y  = __::get($a, 'foo.baz');
        $y2 = __::get($a, 'foo.baz', 'default');
        $y3 = __::get($a, 'foo.baz', function () {
            return 'default_from_callback';
        });
        $z = __::get($a, 'baz.foo.obj');

        // Assert
        $this->assertEquals('ter', $x);
        $this->assertEquals('ter', $x2);
        $this->assertNull($y);
        $this->assertEquals('default', $y2);
        $this->assertEquals('default_from_callback', $y3);
        $this->assertEquals($o, $z);
    }

    public function testGroupByString()
    {
        $a = [
            ['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'School bus'],
            ['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'Manhole'],
            ['state' => 'IN', 'city' => 'Plainfield', 'object' => 'Basketball'],
            ['state' => 'CA', 'city' => 'San Diego', 'object' => 'Light bulb'],
            ['state' => 'CA', 'city' => 'Mountain View', 'object' => 'Space pen'],
        ];

        $grouped = __::groupBy($a, 'state');
        $this->assertCount(2, $grouped);
        $this->assertArrayHasKey('CA', $grouped);
    }

    public function testGroupByStringNested()
    {
        $a = [
            ['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'School bus'],
            ['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'Manhole'],
            ['state' => 'IN', 'city' => 'Plainfield', 'object' => 'Basketball'],
            ['state' => 'CA', 'city' => 'San Diego', 'object' => 'Light bulb'],
            ['state' => 'CA', 'city' => 'Mountain View', 'object' => 'Space pen'],
        ];

        $grouped = __::groupBy($a, 'state', 'city');
        $this->assertCount(2, $grouped);
        $this->assertCount(2, $grouped['IN']);
        $this->assertArrayHasKey('Indianapolis', $grouped['IN']);
    }

    public function testGroupByInteger()
    {
        $a = [
            ['IN', 'Indianapolis', 'School bus'],
            ['IN', 'Indianapolis', 'Manhole'],
            ['IN', 'Plainfield', 'Basketball'],
            ['CA', 'San Diego', 'Light bulb'],
            ['CA', 'Mountain View', 'Space pen'],
        ];

        $grouped = __::groupBy($a, 1);
        $this->assertCount(4, $grouped);
        $this->assertArrayHasKey('Indianapolis', $grouped);
    }

    public function testGroupByObjectProperties()
    {
        $a = [
            (object)['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'School bus'],
            (object)['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'Manhole'],
            (object)['state' => 'IN', 'city' => 'Plainfield', 'object' => 'Basketball'],
            (object)['state' => 'CA', 'city' => 'San Diego', 'object' => 'Light bulb'],
            (object)['state' => 'CA', 'city' => 'Mountain View', 'object' => 'Space pen'],
        ];

        $grouped = __::groupBy($a, 'state');
        $this->assertCount(2, $grouped);
        $this->assertArrayHasKey('CA', $grouped);
    }

    public function testGroupByCallable()
    {
        $a = [
            (object)['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'School bus'],
            (object)['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'Manhole'],
            (object)['state' => 'IN', 'city' => 'Plainfield', 'object' => 'Basketball'],
            (object)['state' => 'CA', 'city' => 'San Diego', 'object' => 'Light bulb'],
            (object)['state' => 'CA', 'city' => 'Mountain View', 'object' => 'Space pen'],
        ];

        $grouped = __::groupBy($a, function ($value) {
            return $value->city;
        });
        $this->assertCount(4, $grouped);
        $this->assertArrayHasKey('Indianapolis', $grouped);
    }

    public function testHasKeys()
    {
        // Arrange
        $a = ['foo' => 'bar'];

        // Act
        $x = __::hasKeys($a, ['foo', 'foz'], false);
        $y = __::hasKeys($a, ['foo', 'foz'], true);

        // Assert
        $this->assertFalse($x);
        $this->assertFalse($y);

        //Rearrange
        $a['foz'] = 'baz';

        //React
        $x = __::hasKeys($a, ['foo', 'foz'], false);
        $y = __::hasKeys($a, ['foo', 'foz'], true);

        // Assert
        $this->assertTrue($x);
        $this->assertTrue($y);

        //Rearrange
        $a['xxx'] = 'bay';

        //React
        $x = __::hasKeys($a, ['foo', 'foz'], false);
        $y = __::hasKeys($a, ['foo', 'foz'], true);

        // Assert
        $this->assertTrue($x);
        $this->assertFalse($y);
    }

    public function testLast()
    {
        // Arrange
        $a = [1, 2, 3, 4, 5];

        // Act
        $x = __::last($a, 2);
        $y = __::last($a);

        // Assert
        $this->assertEquals([4, 5], $x);
        $this->assertEquals(5, $y);
    }

    public function testMap()
    {
        // Arrange
        $a = [1, 2, 3];

        // Act
        $x = __::map($a, function($n) {
            return $n * 3;
        });

        // Assert
        $this->assertEquals([3, 6, 9], $x);
    }

    public function testMax()
    {
        // Arrange
        $a = [1, 2, 3];

        // Act
        $x = __::max($a);

        // Assert
        $this->assertEquals(3, $x);
    }


    public function testMin()
    {
        // Arrange
        $a = [1, 2, 3];

        // Act
        $x = __::min($a);

        // Assert
        $this->assertEquals(1, $x);
    }

    public function testPluck()
    {
        // Arrange
        $a = [
            ['foo' => 'bar',  'bis' => 'ter',  '' => 0],
            ['foo' => 'bar2', 'bis' => 'ter2', '' => 1],
        ];

        $b = new \stdClass();
        $b->one = new \stdClass();
        $b->one->foo = 'bar';
        $b->two = new \stdClass();
        $b->two->foo = 'bar2';
        $b->three = new \stdClass();
        $c = [$b->one, $b->two];

        $d = [
            ['foo' => ['bar' => ['baz' => 1]]],
            ['foo' => ['bar' => ['baz' => 2]]]
        ];
        $e = new \stdClass();
        $e->one = new \stdClass();
        $e->one->foo = new \stdClass();
        $e->one->foo->bar = ['baz' => 1];
        $e->two = new \stdClass();
        $e->two->foo = new \stdClass();
        $e->two->foo->bar = new \stdClass();
        $e->two->foo->bar->baz = 2;

        // Act
        $x  = __::pluck($a, 'foo');
        $x2 = __::pluck($a, '');

        $y  = __::pluck($b, 'foo');
        $y2  = __::pluck($c, 'foo');

        $z = __::pluck($d, 'foo.bar.baz');
        $z2 = __::pluck($e, 'foo.bar.baz');

        // Assert
        $this->assertEquals(['bar', 'bar2'], $x);
        $this->assertEquals([0, 1], $x2);

        $this->assertEquals(['bar', 'bar2', null], $y);
        $this->assertEquals(['bar', 'bar2'], $y2);

        $this->assertEquals([1, 2], $z);
        $this->assertEquals([1, 2], $z2);
    }

    public function testReduceArray()
    {
        // Arrange
        // TODO Get examples from Lodash (shameless).
        // TODO With Std objects.
        // TODO With class objects.
        // (object)
        $a = [1, 2, 3];
        $b = [
            10659489,
            1578484,
            1620331,
            935440,
            944022,
            1037939,
        ];
        $c = [
            ['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'School bus'],
            ['state' => 'IN', 'city' => 'Indianapolis', 'object' => 'Manhole'],
            ['state' => 'IN', 'city' => 'Plainfield', 'object' => 'Basketball'],
            ['state' => 'CA', 'city' => 'San Diego', 'object' => 'Light bulb'],
            ['state' => 'CA', 'city' => 'Mountain View', 'object' => 'Space pen'],
        ];
        $aReducer = function ($accumulator, $value) {
            return $accumulator + $value;
        };
        $bReducer = function ($accumulator, $value, $index) {
            if ($index === 0) {
                $this->assertEquals(10659489, $accumulator);
            }
            return $accumulator + $value;
        };
        $cIndex = 0;
        $cReducer = function ($accumulator, $value, $index, $collection) use(&$c, &$cIndex) {
            $this->assertEquals($c, $collection);
            $this->assertEquals($cIndex++, $index);
            if (isset($accumulator[$value['city']])) {
                $accumulator[$value['city']]++;
            } else {
                $accumulator[$value['city']] = 1;
            }
            return $accumulator;
        };

        // Act
        $x = __::reduce($a, $aReducer, 2);
        $y = __::reduce($b,$bReducer);
        $z = __::reduce($c, $cReducer, []);

        // Assert
        $this->assertEquals(8, $x);
        $this->assertEquals(27435194, $y);
        $this->assertEquals([
            'Indianapolis' => 2,
            'Plainfield' => 1,
            'San Diego' => 1,
            'Mountain View' => 1,
        ], $z);
    }

    public function testReduceStdClass()
    {
        // Arrange
        $a = new \stdClass();
        $a->paris = 10659489;
        $a->marseille = 1578484;
        $a->lyon = 1620331;
        $a->toulouse = 935440;
        $a->nice = 944022;
        $a->lille = 1037939;
        $aReducer = function ($accumulator, $value) {
            return $accumulator + $value;
        };

        // Act
        $x = __::reduce($a, $aReducer, 0);

        // Assert
        $this->assertEquals(16775705, $x);
    }

    public function testSet()
    {
        // Arrange
        $a = ['foo' => ['bar' => 'ter']];

        // Act
        $x = __::set($a, 'foo.baz.ber', 'fer');
        $y = __::set($a, 'foo.bar', 'fer2', true);

        // Assert
        $this->assertEquals(['ber' => 'fer'], $x['foo']['baz']);
        $this->assertEquals(['foo' => ['bar' => 'fer2']], $y);
    }

    public function testSetStrictException()
    {
        if (method_exists($this, 'expectException')) {
            // new phpunit
            $this->expectException('\Exception');
        } else {
            // old phpunit
            $this->setExpectedException('\Exception');
        }

        // Arrange
        $a = [
            'foo' => ['bar' => 'ter']
        ];

        // Act
        __::set($a, 'foo.bar.not_exist', 'baz', true);
    }

    public function testUnease()
    {
        // Arrange
        $a = ['foo.bar' => 'ter', 'baz.0' => 'b', 'baz.1' => 'z'];

        // Act
        $x = __::unease($a);

        // Assert
        $this->assertEquals(2, count($x));
        $this->assertEquals(['foo' => ['bar' => 'ter'], 'baz' => ['b', 'z']], $x);
    }

    public function testWhere()
    {
        // Arrange
        $a = [
            ['name' => 'fred',   'age' => 32],
            ['name' => 'maciej', 'age' => 16]
        ];

        // Act
        $x = __::where($a, ['age' => 16]);
        $x2 = __::where($a, ['age' => 16, 'name' => 'fred']);
        $x3 = __::where($a, ['name' => 'maciej', 'age' => 16]);

        // Assert
        $this->assertEquals([$a[1]], $x);
        $this->assertEquals([], $x2);
        $this->assertEquals([$a[1]], $x3);
    }

    // ...
}
