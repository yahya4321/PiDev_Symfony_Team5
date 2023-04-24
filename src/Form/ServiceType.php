<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('servlib')
            ->add('servdesc',TextareaType::class)
            ->add('servprix')
            ->add('servdispo')
            ->add('note')
            ->add('servville')
            ->add('servimg',FileType::class,array("data_class"=>null))
            ->add('catlib', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'catlib',
                'choice_value' => 'catlib',
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }


}