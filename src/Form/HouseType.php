<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\House;
use FOS\CKEditorBundle\Config\CKEditorConfiguration;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class HouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('image',FileType::class,[
                'label' => 'House Main Image',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*', //all image types
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
            ])
            ->add('address')
            ->add('city', ChoiceType::class,[
                'choices' =>[
                    'İstanbul' => 'Istanbul',
                    'Ankara' => 'Ankara',
                    'İzmir' => 'Izmir',
                    'Bursa' => 'Bursa',
                    'Antalya' => 'Antalya',
                    'Karabük' => 'Karabuk',
                    'Gaziantep' => 'Gaziantep',
                    'Artvin' => 'Artvin',
                ],
            ])
            ->add('location')
            ->add('status',ChoiceType::class,[
                'choices' =>[
                    'New' => 'New',
                    'True' => 'True',
                    'False' => 'False'],
            ])
            ->add('detail',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    //....
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => House::class,
        ]);
    }
}
