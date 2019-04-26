<?php

namespace spec\Germ\Domain\Model\Person;

use Germ\Domain\Model\Person\Person;
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
        $this->beConstructedWith(self::FIRST_NAME, self::LAST_NAME);
    }

    function it_has_a_name()
    {
        $this->getFullName()->shouldReturn(self::FIRST_NAME . Person::FULL_NAME_SEPARATOR . self::LAST_NAME);
    }
}
