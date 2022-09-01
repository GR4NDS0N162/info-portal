<?php

namespace Application\Form\Login;

use Application\Helper\FieldsetMapper;
use Laminas\Form\Element;
use Laminas\Form\Form;

class RecoverForm extends Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('class', 'row gy-3 needs-validation');
        $this->setAttribute('novalidate', true);

        $this->add([
            'name'       => 'email',
            'type'       => Element\Email::class,
            'attributes' => [
                'class'       => 'form-control',
                'placeholder' => 'name@example.com',
                'required'    => 'required',
                'pattern'     => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
            ],
            'options'    => [
                'label'            => 'E-mail',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
        ]);

        $this->add([
            'name'       => 'submit_button',
            'type'       => Element\Button::class,
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-lg btn-outline-danger w-100',
            ],
            'options'    => [
                'label' => 'Восстановить',
            ],
        ], [
            'priority' => -10 ** 9,
        ]);

        FieldsetMapper::setAttributes($this, [
            'children' => [
                'email'         => 'col-12',
                'submit_button' => 'col-12',
            ],
        ]);
    }
}