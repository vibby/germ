<?php

namespace spec\Germ\Domain\Model\Church;

use Germ\Domain\Model\Church\Church;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChurchSpec extends ObjectBehavior
{
    const CHURCH_NAME = 'Church name';

    function it_is_initializable()
    {
        $this->shouldHaveType(Church::class);
    }

    function let()
    {
        $this->beConstructedWith(self::CHURCH_NAME);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(self::CHURCH_NAME);
    }
}
