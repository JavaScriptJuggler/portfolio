<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordChangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->setAction('/administrator/change-password-profile-details')
            ->setMethod('POST')
            ->add('oldPassword', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Old Password',
                'label_attr' => array('class' => 'form-label'),
                "constraints" => [
                    new UserPassword([
                        'message' => "Current Password Dosen't match"
                    ])
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'mapped' => false,
                // 'required' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'New Password',
                'label_attr' => array('class' => 'form-label'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
