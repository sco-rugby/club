<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Model\RapportOvale;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ImportType extends AbstractType {

    public function __construct(private UrlGeneratorInterface $router) {
        return;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $url = $this->router->generate('import_index');
        $builder
                ->add('rapport', EnumType::class, [
                    'label' => 'import.rapport.label',
                    'help' => 'import.rapport.help',
                    'help_html' => true,
                    'class' => RapportOvale::class,
                    'required' => true,
                    'expanded' => true,
                    'choice_label' => static function (\UnitEnum $choice): string {
                        return $choice->value;
                    },
                    'choice_value' => static function (?\UnitEnum $choice): ?string {
                        if (null === $choice) {
                            return null;
                        }
                        return (string) $choice->name;
                    }
                ])
                ->add('fichier', DropzoneType::class, [
                    'label' => 'import.fichier.label',
                    'help' => 'import.fichier.help',
                    'help_html' => true,
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'placeholder.dropzone'
                    ],
                    'mapped' => false,
                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid Excel document',
                                ])
                    ],
                ])
        ;
    }

}
