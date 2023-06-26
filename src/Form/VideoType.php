<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Count;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters',
                        'min' => 3,
                        'minMessage' => 'Title cannot be shorter than {{ limit }} characters',
                    ])
                ]
            ])
            ->add('file', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '200M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/mpeg',
                        ],
                        'mimeTypesMessage' => 'Only video files in MP4, MPEG, QuickTime, or Matroska format are allowed',
                        'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Maximum allowed size is {{ limit }} {{ suffix }}'
                    ])
                ]
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'tag-input'
                ],
                'entry_options' => [
                    'attr' => [
                        'class' => 'tag-field'
                    ],
                ],
                'constraints' => [
                    new Count([
                        'max' => 3,
                        'maxMessage' => 'You can specify a maximum of {{ limit }} tags'
                    ])
                ],
            ])
            ->add('Submit', SubmitType::class);
    }
}