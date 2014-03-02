<?php

class MongoCollectionTest extends BaseTest
{
    public function testInsert()
    {
        $coll = $this->getTestDB()->selectCollection('testInsert');
        
        $data = ['foo' => 'bar' ];
        $coll->insert($data);

        $this->assertCount(1, $coll->find());
        $this->assertEquals($data, $coll->findOne());
    }

    public function testInsertWithId()
    {
        $coll = $this->getTestDB()->selectCollection('testInsert');

        $data = [
            '_id' => new MongoId('000000000000000000000042'),
            'foo' => 'bar'
        ];

        $coll->insert($data);

        $this->assertCount(1, $coll->find());
        $this->assertEquals($data, $coll->findOne());
    }

    public function testRemove()
    {
        $coll = $this->getTestDB()->selectCollection('testDelete');
        
        $data = [
            '_id' => new MongoId('000000000000000000000001'),
            'foo' => 'bar'
        ];
        $coll->insert($data);

        $data = [
            '_id' => new MongoId('000000000000000000000002'),
            'foo' => 'qux'
        ];
        $coll->insert($data);

        $this->assertCount(2, $coll->find());

        $coll->remove(['foo' => 'qux']);
        $result = iterator_to_array($coll->find());
        $this->assertCount(1, $result);
        $this->assertSame('bar', current($result)['foo']);

        $data = [
            '_id' => new MongoId('000000000000000000000003'),
            'foo' => 'qux'
        ];
        $coll->insert($data);

        $data = [
            '_id' => new MongoId('000000000000000000000004'),
            'foo' => 'qux'
        ];
        $coll->insert($data);
        
        $this->assertCount(3, $coll->find());

        $coll->remove(['foo' => 'qux']);
        $result = iterator_to_array($coll->find());
        $this->assertCount(1, $result);
        $this->assertSame('bar', current($result)['foo']);
    }

    public function testDrop()
    {
        $coll = $this->getTestDB()->selectCollection('testDrop');

        $data = [
            '_id' => new MongoId('000000000000000000000042'),
            'foo' => 'bar'
        ];

        $coll->insert($data);

        $this->assertEquals(1, $coll->count());
        $coll->drop();
        $this->assertEquals(0, $coll->count());
    }

    public function testCount()
    {
        $coll = $this->getTestDB()->selectCollection('testCount');
        $coll->drop();
        $this->assertEquals(0, $coll->count());

        $data = [
            '_id' => new MongoId('000000000000000000000042'),
            'foo' => 'bar'
        ];

        $coll->insert($data);

        $this->assertEquals(1, $coll->count());
     }

    public function testGetName()
    {
        $coll = $this->getTestDB()->selectCollection('testGetName');
        $this->assertEquals('testGetName', $coll->getName());
     }

    public function testUpdate()
    {
        $data = ['foo'=>'bar'];

        $coll = $this->getTestDB()->selectCollection('testUpdate');
        $coll->insert($data);
        $result = iterator_to_array($coll->find());
        $this->assertCount(1, $result);

        $record = current($result);
        $this->assertEquals('bar', $record['foo']);
        $record['foo'] = 'notbar';
        $coll->update(['_id'=> $record['_id']], ['$set'=>['foo'=>'notbar']]);
        
        $result = iterator_to_array($coll->find(['_id'=> $record['_id']]));
        $this->assertCount(1, $result);
        $this->assertEquals('notbar', $record['foo']);
     }

    public function testSave()
    {
        $data = ['foo'=>'bar'];

        $coll = $this->getTestDB()->selectCollection('testUpdate');
        $coll->insert($data);
        $result = iterator_to_array($coll->find());
        $this->assertCount(1, $result);

        $record = current($result);
        $this->assertEquals('bar', $record['foo']);
        $record['foo'] = 'notbar';
        $coll->save($record);


        $result = iterator_to_array($coll->find(['_id'=> $record['_id']]));
        $this->assertCount(1, $result);
        $this->assertEquals('notbar', $record['foo']);
    }

}
