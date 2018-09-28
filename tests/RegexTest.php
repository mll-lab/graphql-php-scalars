<?php

use GraphQL\Error\InvariantViolation;
use MLL\GraphQLScalars\Regex;

class RegexTest extends \PHPUnit\Framework\TestCase
{
    /** @var Regex */
    protected $fooRegexScalar;
    
    public function setUp()
    {
        $this->fooRegexScalar = new class extends Regex
        {
            /**
             * Return the Regex that the values are validated against.
             *
             * Must be a valid
             *
             * @return string
             */
            protected function regex(): string
            {
                return '/foo/';
            }
        };
    }
    
    public function testSerializeThrowsIfUnserializableValueIsGiven()
    {
        $this->expectException(InvariantViolation::class);
        
        $this->fooRegexScalar->serialize(
            new class
            {
            }
        );
    }
    
    public function testSerializeThrowsIfRegexIsNotMatched()
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessageRegExp('/did not match the regex/');
        
        $this->fooRegexScalar->serialize('bar');
    }
    
    public function testSerializePassesWhenRegexMatches()
    {
        $serializedResult = $this->fooRegexScalar->serialize('foo');
        $this->assertSame('foo', $serializedResult);
    }
    
    public function testSerializePassesForStringableObject()
    {
        $serializedResult = $this->fooRegexScalar->serialize(
            new class {
                public function __toString(): string
                {
                    return 'Contains foo right?';
                }
            }
        );
        $this->assertSame('Contains foo right?', $serializedResult);
    }
}
