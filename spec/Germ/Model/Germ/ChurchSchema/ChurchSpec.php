<?php

namespace spec\Germ\Model\Germ\ChurchSchema;

use Germ\Model\Germ\ChurchSchema\Church;
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
        $this->beConstructedWith(['name' => self::CHURCH_NAME]);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(self::CHURCH_NAME);
    }
}
