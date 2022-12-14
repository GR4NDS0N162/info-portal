<?php

namespace Application\Fieldset;

use Application\Helper\FieldsetMapper;
use Application\Model\Repository\StatusRepositoryInterface;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Fieldset;

class StatusFieldset extends Fieldset
{
    private StatusRepositoryInterface $statusRepository;

    public function __construct($statusRepository)
    {
        parent::__construct();

        $this->statusRepository = $statusRepository;
    }

    public function init()
    {
        parent::init();

        $statuses = $this->statusRepository->findAllStatuses();

        foreach ($statuses as $status) {
            $this->add([
                'name'       => $status->getName(),
                'type'       => Checkbox::class,
                'attributes' => [
                    'class'             => 'form-check-input',
                    FieldsetMapper::KEY => 'col-12 col-sm-6',
                    'id'                => uniqid('checkbox_', true),
                ],
                'options'    => [
                    'label'              => $status->getLabel(),
                    'label_attributes'   => UserFieldset::DEFAULT_CHECK_LABEL_ATTRIBUTES,
                    'use_hidden_element' => false,
                ],
            ]);
        }
    }
}