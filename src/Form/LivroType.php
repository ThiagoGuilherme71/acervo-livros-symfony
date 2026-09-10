<?php

namespace App\Form;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Range;

class LivroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'constraints' => [
                    new NotBlank(message: 'O título é obrigatório.'),
                    new Length(max: 40, maxMessage: 'O título pode ter no máximo {{ limit }} caracteres.'),
                ],
            ])
            ->add('editora', TextType::class, [
                'label' => 'Editora',
                'constraints' => [
                    new NotBlank(message: 'A editora é obrigatória.'),
                    new Length(max: 40, maxMessage: 'A editora pode ter no máximo {{ limit }} caracteres.'),
                ],
            ])
            ->add('edicao', IntegerType::class, [
                'label' => 'Edição',
                'constraints' => [
                    new NotBlank(message: 'A edição é obrigatória.'),
                    new Positive(message: 'A edição deve ser um número positivo.'),
                    new Range(
                        max: 2147483647,
                        maxMessage: 'A edição não pode ser maior que {{ limit }}.'
                    ),
                ],
            ])
            ->add('anoPublicacao', TextType::class, [
                'label' => 'Ano de Publicação (yyyy)',
                'attr' => [
                    'inputmode' => 'numeric',
                    'pattern' => '[0-9]{4}',
                    'maxlength' => 4,
                ],
                'constraints' => [
                    new NotBlank(message: 'O ano de publicação é obrigatório.'),
                    new Regex(pattern: '/^\d{4}$/', message: 'Informe um ano válido com 4 dígitos.'),
                    new Range(
                        min: 1000,
                        max: (int) date('Y'),
                        notInRangeMessage: 'O ano deve estar entre {{ min }} e {{ max }}.'
                    ),
                ],
            ])
            ->add('valor', MoneyType::class, [
                'label' => 'Valor',
                'currency' => 'BRL',
                'constraints' => [
                    new NotBlank(message: 'O valor é obrigatório.'),
                    new Positive(message: 'O valor deve ser maior que zero.'),
                    new Range(
                        max: 99999999.99,
                        maxMessage: 'O valor não pode ser maior que {{ limit }}.'
                    ),
                ],
            ])
            ->add('autores', EntityType::class, [
                'class' => Autor::class,
                'choice_label' => 'nome',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Autor(es)',
                'constraints' => [
                    new Count(min: 1, minMessage: 'Selecione ao menos um autor.'),
                ],
            ])
            ->add('assuntos', EntityType::class, [
                'class' => Assunto::class,
                'choice_label' => 'descricao',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Assunto(s)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livro::class,
        ]);
    }
}