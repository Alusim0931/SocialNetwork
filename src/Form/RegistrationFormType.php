<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ])
            ->add('gender', ChoiceType::class, [
                'choices'  => [
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'expanded' => true, // Esto hace que se muestren como botones de radio (opciones seleccionables)
                'multiple' => false, // Solo puede seleccionar una opción
                'label' => 'Gender', // Etiqueta del campo
            ])
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text', // Elige un solo campo de texto
                'input' => 'datetime', // Symfony debe esperar un objeto DateTime
                'format' => 'yyyy-MM-dd', // Establece el formato de la fecha
                'label' => 'Birthdate',
            ])

            ->add('bio', TextareaType::class, [
                'required' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('isStudent', CheckboxType::class, [
                'required' => false,
                'label' => 'Are you a student?',
            ])
            ->add('isChef', CheckboxType::class, [
                'required' => false,
                'label' => 'Are you a chef?',
            ])
            ->add('proofOfChefTitle', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Proof of Chef Title (if applicable)',
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image or PDF file.',
                    ]),
                ],
            ])
            ->add('userImage', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Upload your profile image',
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG or PNG).',
                    ]),
                ],
            ]);
    }

public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // or Student::class, if using Student
        ]);
    }
}