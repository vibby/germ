<?php

namespace GermBundle\Filter\Criteria;

use GermBundle\Filter\Criteria\AbstractCriteria;
use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Projection;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

abstract class AbstractCriteriaTerms extends AbstractCriteria
{
    const COUNT_FIELD_NAME = 'terms_count';

    abstract protected static function getLabel();

    abstract protected static function getFields();

    public static function getUrlPrefix()
    {
        return 'term';
    }

    public static function getFormName()
    {
        return 'term';
    }

    public function urlize($data)
    {
        return $data;
    }

    public function unurlize($data)
    {
        return $data;
    }

    public function alterForm(Form &$form)
    {
        $form->add(self::getUrlPrefix(), SearchType::class, [
            'label' => static::getLabel(),
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
        if (!$this->data) {
            return null;
        }
        $where = Where::create();
        foreach (explode(' ', $this->data) as $searchTerm) {
            foreach (static::getFields() as $fieldName) {
                $where->orWhere(Where::createGroupCondition(
                    sprintf('LOWER(%s)', $fieldName),
                    'LIKE',
                    [sprintf('%s%%', strtolower($searchTerm))])
                );
            }
        }

        return $where;
    }

    public function alterProjection(Projection &$projection)
    {
        if (!$this->data) {
            return [];
        }
        $field = '';
        $countResultProjectionValues = [];
        foreach (explode(' ', $this->data) as $searchTerm) {
            foreach (static::getFields() as $fieldName) {
                $field .= $field ? ' + ' : '(';
                $condition = (string) Where::createGroupCondition(
                    sprintf('LOWER(%s)', $fieldName),
                    'LIKE',
                    [sprintf('%s%%', strtolower($searchTerm))]
                );
                $countResultProjectionValues[] = sprintf('%s%%', strtolower($searchTerm));
                $field .= 'case when '.$condition.' THEN 1 ELSE 0 END';
            }
        }
        $field .= ')';
        $projection->setField(self::COUNT_FIELD_NAME, $field);

        return $countResultProjectionValues;
    }

    public function alterOrderBy(&$orderBy)
    {
        if (!$this->data) {
            return null;
        }
        array_unshift($orderBy, self::COUNT_FIELD_NAME.' DESC');
    }
}
