<?php

namespace App\Form;

use App\Entity\TitleDescription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TitleDescriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Title',
                'label_attr' => array('class' => 'form-label'),
                "constraints" => [
                    new NotBlank([
                        'message' => "This Field Cannot Be Blank"
                    ])
                ],
            ])
            ->add('tagline', TextareaType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'style' => 'resize:none'
                ],
                'label' => 'Tagline',
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
            'data_class' => TitleDescription::class,
        ]);
    }
}
