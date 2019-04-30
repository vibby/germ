<?php

namespace spec\Germ\Model\Germ\PersonSchema;

use Germ\Model\Germ\PersonSchema\Person;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PersonSpec extends ObjectBehavior
{
    const FIRST_NAME = 'Vincent';
    const LAST_NAME = 'Duchemin';

    function it_is_initializable()
    {
        $this->shouldHaveType(Person::class);
    }

    function let()
    {
        $this->beConstructedWith([
            'firstname' => self::FIRST_NAME,
            'lastname' => self::LAST_NAME,
        ]);
    }

    function it_has_a_name()
    {
        (string) $this->__toString()->shouldReturn(self::FIRST_NAME . Person::FULL_NAME_SEPARATOR . self::LAST_NAME);
    }
}
