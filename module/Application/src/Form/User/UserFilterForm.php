<?php

namespace Application\Form\User;

use Application\Fieldset\AgeFilterFieldset;
use Application\Fieldset\ProfileFieldset;
use Application\Helper\FieldsetMapper;
use Application\Model\Options\GenderOptions;
use Application\Model\Options\PositionOptions;
use Application\Model\Options\YesNoOptions;
use Laminas\Form\Element;
use Laminas\Form\Form;

class UserFilterForm extends Form
{
    /**
     * @var PositionOptions
     */
    private $positionOptions;

    /**
     * @param PositionOptions $positionOptions
     * @param string          $name
     */
    public function __construct(
        $positionOptions,
        $name = 'UserFilterForm'
    ) {
        parent::__construct($name);

        $this->positionOptions = $positionOptions;
    }

    public function init()
    {
        parent::init();

        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', true);

        $this->add([
            'name'       => 'positionId',
            'type'       => Element\Select::class,
            'attributes' => [
                'class' => 'form-select',
            ],
            'options'    => [
                'label'            => 'Должность',
                'label_attributes' => ProfileFieldset::DEFAULT_LABEL_ATTRIBUTES,
                'options'          => $this->positionOptions->getEnabledOptions(),
            ],
        ]);

        $this->add([
            'name'       => 'gender',
            'type'       => Element\Select::class,
            'attributes' => [
                'class' => 'form-select',
            ],
            'options'    => [
                'label'            => 'Пол',
                'label_attributes' => ProfileFieldset::DEFAULT_LABEL_ATTRIBUTES,
                'options'          => GenderOptions::getOptions(),
            ],
        ]);

        $this->add([
            'name'       => 'age',
            'type'       => AgeFilterFieldset::class,
            'attributes' => [
                'class' => 'row g-3',
            ],
            'options'    => [
                'label' => 'Возраст',
            ],
        ]);

        $this->add([
            'name'       => 'fullnamePhoneEmail',
            'type'       => Element\Textarea::class,
            'attributes' => [
                'class'       => 'form-control',
                'rows'        => '3',
                'placeholder' => 'Иванов Иван Иванович, +79283627374, example@name.com',
            ],
            'options'    => [
                'label'            => 'ФИО, телефон, email',
                'label_attributes' => ProfileFieldset::DEFAULT_LABEL_ATTRIBUTES,
            ],
        ]);

        $this->add([
            'name'       => 'submitButton',
            'type'       => Element\Button::class,
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-outline-success w-100',
            ],
            'options'    => [
                'label' => 'Применить фильтры',
            ],
        ], [
            'priority' => -10 ** 9,
        ]);

        $this->add([
            'name'       => 'active',
            'type'       => Element\Select::class,
            'attributes' => [
                'class' => 'form-select',
            ],
            'options'    => [
                'label'            => 'Активен',
                'label_attributes' => ProfileFieldset::DEFAULT_LABEL_ATTRIBUTES,
                'options'          => YesNoOptions::getActiveOptions(),
            ],
        ]);

        FieldsetMapper::setAttributes($this, [
            'children' => [
                'positionId'         => 'col-12',
                'gender'             => 'col-12',
                'age'                => [
                    'value'    => 'col-12',
                    'children' => [
                        'min' => 'col-12',
                        'max' => 'col-12',
                    ],
                ],
                'fullnamePhoneEmail' => 'col-12',
                'active'             => 'd-none',
                'submitButton'       => 'col-12',
            ],
        ]);
    }
}
