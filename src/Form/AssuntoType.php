<?php

namespace App\Form;

use App\Entity\Assunto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AssuntoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descricao', TextType::class, [
                'label' => 'Descrição',
                'constraints' => [
                    new NotBlank(message: 'A descrição do assunto é obrigatória.'),
                    new Length(max: 20, maxMessage: 'A descrição pode ter no máximo {{ limit }} caracteres.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assunto::class,
        ]);
    }
}