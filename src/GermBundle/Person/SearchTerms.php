<?php

namespace GermBundle\Person;

use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Projection;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

class SearchTerms extends AbstractSearchItem
{
    const NAME = 'terms';
    const COUNT_FIELD_NAME = 'terms_count';

    public function alterForm(Form &$form)
    {
        $form->add(self::NAME, SearchType::class, [
            'label' => 'Lastname Firstname',
            'data' => $this->data,
            'required' => false,
            'render_optional_text' => false,
        ]);
    }

    public function highlight($string)
    {
        $terms = explode(' ', $this->data);
        preg_match('~\w+~', implode(' ', $terms), $matches);
        if (!$matches) {
            return $string;
        }
        $re = '# '.implode('| ',$terms).'|^'.implode('|^',$terms).'#i';

        return preg_replace($re, '<strong>$0</strong>', $string);
    }

    public function buildWhere()
    {
        $where = Where::create();
        foreach (explode(' ', $this->data) as $searchTerm) {
            $where->orWhere(Where::createGroupCondition('LOWER(lastname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]));
            $where->orWhere(Where::createGroupCondition('LOWER(firstname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]));
        }

        return $where;
    }

    public function alterProjection(Projection &$projection)
    {
        $field = '';
        $countResultProjectionValues = [];
        foreach (explode(' ', $this->data) as $searchTerm) {
            $field .= $field ? ' + ' : '(';
            $condition = (string) Where::createGroupCondition('LOWER(lastname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]);
            $countResultProjectionValues[] = sprintf('%s%%', strtolower($searchTerm));
            $field .= 'case when '.$condition.' THEN 1 ELSE 0 END';
            $field .= ' + ';
            $condition = (string) Where::createGroupCondition('LOWER(firstname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]);
            $countResultProjectionValues[] = sprintf('%s%%', strtolower($searchTerm));
            $field .= 'case when '.$condition.' THEN 1 ELSE 0 END';
        }
        $field .= ')';
        $projection->setField(self::COUNT_FIELD_NAME, $field);

        return $countResultProjectionValues;
    }

    public function alterOrderBy(&$orderBy)
    {
        array_unshift($orderBy, self::COUNT_FIELD_NAME.' DESC');
    }
}
