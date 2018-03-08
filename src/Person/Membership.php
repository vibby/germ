<?php

namespace Germ\Person;

class Membership
{
    public static function getActChoices()
    {
        return [
            'Baptism' => 'baptism',
            'Profession of faith' => 'profession_faith',
            'Recommendation letter' => 'recommendation_letter',
            'Transfer' => 'transfer',
        ];
    }
}
