<?php

namespace App\Form;

use App\Entity\Video;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VideoType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'iframe',
                TextareaType::class,
                $this->getConfiguration("Iframe de la vidéo à ajouter :", "Donnez l'Iframe de la vidéo du cours")
            )
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration("Titre de la vidéo à ajouter :", "Donnez le titre de la vidéo du cours")
            )
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
