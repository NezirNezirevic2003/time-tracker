<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\API;

use App\Entity\Timesheet;
use App\Form\TimesheetEditForm;
use App\Form\Type\BillableType;
use App\Form\Type\TagsInputType;
use App\Form\Type\TimesheetBillableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimesheetApiEditForm extends TimesheetEditForm
{
    protected function addBillable(FormBuilderInterface $builder, array $options)
    {
        if (!$options['include_billable']) {
            return;
        }

        $builder->add('billable', BillableType::class, []);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (\array_key_exists('billable', $data)) {
                    $event->getForm()->add('billableMode', TimesheetBillableType::class, []);
                    if ($data['billable'] === true) {
                        $data['billableMode'] = Timesheet::BILLABLE_YES;
                    } elseif ($data['billable'] === false) {
                        $data['billableMode'] = Timesheet::BILLABLE_NO;
                    }
                }
                $event->setData($data);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('metaFields');

        if ($builder->has('user')) {
            $builder->get('user')->setRequired(false);
        }

        if ($builder->has('tags')) {
            $builder->remove('tags');
            // @deprecated for BC reasons here, arrays will be supported in 2.0
            $builder->add('tags', TagsInputType::class, [
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_duration' => false,
            // overwritten and changed to default "true",
            // because the docs are cached without these fields otherwise
            'include_user' => true,
            'include_exported' => true,
            'include_billable' => true,
            'include_rate' => true,
        ]);
    }
}
