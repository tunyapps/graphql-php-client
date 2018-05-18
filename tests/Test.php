<?php

use Base\SubQuery;
use Base\QueryVariables;
use PHPUnit\Framework\TestCase;
use Exceptions\SyntaxErrorException;
use Exceptions\BuilderErrorException;

final class Test extends TestCase
{
    /**
     * Testing query variables definition
     */
    public function testQueryVariables()
    {
        $builder = new Query();
        $query = $builder->method('foo', function(QueryVariables $variables) {
            $variables->string('string', 'this is <i>a</i> "test" \'string\' value');
            $variables->integer('integer', 123456);
            $variables->boolean('boolean_false', false);
            $variables->boolean('boolean_true', true);
            $variables->float('float', 12.3456);
            $variables->id('id', 123456);
        })->build(false);

        $this->assertContains('string:"this is <i>a</i> \"test\" \'string\' value"', $query);
        $this->assertContains('integer:123456', $query);
        $this->assertContains('boolean_false:0', $query);
        $this->assertContains('boolean_true:1', $query);
        $this->assertContains('float:12.3456', $query);
        $this->assertContains('id:123456', $query);
    }

    /**
     * Testing query fields definition
     */
    public function testQueryFields()
    {
        $builder = new Query();
        $query = $builder->fields([
            'foo',
            'bar' => function(SubQuery $query) {
                $query->fields(['field_1', 'field_2']);
                $query->field('field_3');
            },
        ])
        ->field('baz')
        ->build(false);

        $this->assertContains('{foo,bar{field_1,field_2,field_3},baz}', $query);
    }

    /**
     * Testing query wrong procedure call
     */
    public function testWrongMagicCall()
    {
        $this->expectException(SyntaxErrorException::class);
        $builder = new Query();
        $builder->method('foo', $builder);
    }

    /**
     * Testing query without remote procedure
     * name
     */
    public function testWithoutMethod()
    {
        $builder = new Query();
        $query = $builder->field('foo')
            ->build(false);

        $this->assertEquals('query {foo}', $query);
    }

    /**
     * Testing query without procedure variables
     */
    public function testWithoutVariables()
    {
        $builder = new Query();
        $query = $builder->field('foo')
            ->build();

        $this->assertNotContains('variables', $query);
    }

    /**
     * Testing query without nested fields
     */
    public function testWithoutNestedFields()
    {
        $builder = new Query();
        $query = $builder->field('foo', function(SubQuery $query) {
            $query->arg('bar', '123');
            $query->fields(['field_1', 'field_2']);
            $query->field('field_3');
        })->build(false);

        $this->assertContains('field_1,field_2,field_3', $query);
    }

    /**
     * Testing query fields definition syntax
     * error
     */
    public function testFieldsSyntaxError()
    {
        $this->expectException(SyntaxErrorException::class);
        $builder = new Query();
        $builder->field('foo', $builder);
    }

    /**
     * Testing sub query arguments definition
     * syntax error
     */
    public function testArgsSyntaxError()
    {
        $this->expectException(SyntaxErrorException::class);
        $builder = new Query();
        $builder->field('foo', function(SubQuery $query) {
            $query->arg('bar', 123);
        });
    }

    /**
     * Testing query empty sub query definition
     */
    public function testEmptySubQuery()
    {
        $this->expectException(BuilderErrorException::class);

        $builder = new Query();
        $builder->field('foo', function(SubQuery $query) {})
            ->build();
    }

    /**
     * Testing sub query without arguments
     */
    public function testSubQueryWithoutArgs()
    {
        $builder = new Query();
        $query = $builder->field('foo', function(SubQuery $query) {
            $query->field('bar');
        })->build(false);

        $this->assertContains('foo{bar}', $query);
    }

    /**
     * Testing sub query without fields
     */
    public function testSubQueryWithoutFields()
    {
        $this->expectException(BuilderErrorException::class);

        $builder = new Query();
        $builder->field('foo', function(SubQuery $query) {
            $query->arg('bar', '123');
        })->build(false);
    }
}