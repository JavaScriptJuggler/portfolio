<?php

namespace App\Form;

use App\Entity\Phrase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PharseEntryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase_name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'mapped' => false,
                'label' => 'Phrase',
                'label_attr' => array('class' => 'form-label'),
                "constraints" => [
                    new NotBlank([
                        'message' => "This Field Cannot Be Blank"
                    ])
                ],
            ])
            ->add('phrase_description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'style' => 'resize:none;',
                ],
                'mapped' => false,
                'label' => 'Phrase Description',
                'label_attr' => array('class' => 'form-label'),
                "constraints" => [
                    new NotBlank([
                        'message' => "This Field Cannot Be Blank"
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Phrase::class,
        ]);
    }
}
