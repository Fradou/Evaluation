<?php

namespace MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')->add('artiste')
            ->add('genre', ChoiceType::class, array ('choices'=> array('Hiphop'=>'Hiphop', 'Soul'=>'Soul', 'Rock'=>'Rock')))
            ->add('support', ChoiceType::class, array ('choices'=> array('Vinyl'=>'Vinyl', 'CD'=>'CD', 'Cassette'=>'Cassette')))
            ->add('img', FileType::class, array('label' => 'Album cover : '));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MediaBundle\Entity\Album'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mediabundle_album';
    }


}
