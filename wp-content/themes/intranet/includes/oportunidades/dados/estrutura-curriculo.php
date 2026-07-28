<?php

return [

    'identificacao' => [

        'titulo' => 'Identificação do Candidato',

        'subsecoes' => [

            'dados_pessoais' => [

                'titulo' => 'Dados Pessoais e Funcionais',
                'campos' => [
                    'nome_completo' => [
                        'label' => 'Nome Completo',
                    ],
                    'nome_social' => [
                        'label' => 'Nome Social',
                    ],
                    'rf' => [
                        'label' => 'RF',
                    ],
                ],

            ],

            'identificacao_pessoal' => [

                'titulo' => 'Identificação Pessoal',
                'campos' => [
                    'data_nascimento' => [
                        'label' => 'Data de Nascimento',
                    ],
                    'identificacao_racial' => [
                        'label' => 'Como você se identifica?',
                    ],
                    'identidade_genero' => [
                        'label' => 'Qual sua identidade de gênero?',
                    ],
                ],

            ],

            'informacoes_complementares' => [

                'titulo' => 'Informações Complementares',
                'campos' => [
                    'possui_deficiencia' => [
                        'label' => 'Você é uma pessoa com deficiência?',
                    ],
                    'necessita_adaptacao' => [
                        'label' => 'Você precisa de algum tipo de adaptação para executar trabalhos de escritório?',
                    ],
                    'descreva_adaptacao' => [
                        'label' => 'Se sim, por gentileza, descreva as adaptações que seriam necessárias ao ambiente ou tecnologias assistivas.',
                    ],
                    'servidor_readaptado' => [
                        'label' => 'Você é servidor em readaptação funcional (readaptado)?',
                    ],
                    'readaptado_necessita' => [
                        'label' => 'Você precisa de algum tipo de adaptação para executar trabalhos de escritório?',
                    ],
                    'readaptado_descricao' => [
                        'label' => 'Se sim, por gentileza, descreva as adaptações que seriam necessárias ao ambiente ou tecnologias assistivas.',
                    ],
                ],

            ],

            'contato' => [

                'titulo' => 'Contato',
                'campos' => [
                    'telefone_whatsapp' => [
                        'label' => 'Telefone de contato (WhatsApp)',
                    ],
                    'telefone_opcional' => [
                        'label' => 'Telefone de contato (Opcional)',
                    ],
                    'email_principal' => [
                        'label' => 'E-mail Institucional ou de Uso Principal',
                    ],
                    'email_secundario' => [
                        'label' => 'E-mail secundário',
                    ],
                ],

            ],

            'lotacao_exercicio' => [

                'titulo' => 'Lotação e Exercício',
                'campos' => [
                    'concluiu_estagio' => [
                        'label' => 'Concluiu o estágio probatório?',
                    ],
                    'cargo_efetivo' => [
                        'label' => 'Qual é o seu cargo efetivo?',
                    ],
                    'cargo_outro' => [
                        'label' => 'Informe o cargo',
                    ],
                    'dre_lotacao' => [
                        'label' => 'DRE de lotação',
                    ],
                    'unidade_lotacao' => [
                        'label' => 'Unidade de Lotação',
                    ],
                    'dre_exercicio' => [
                        'label' => 'DRE de Exercício',
                    ],
                    'unidade_exercicio' => [
                        'label' => 'Unidade de Exercício',
                    ],
                    'acumula_cargo' => [
                        'label' => 'Você acumula cargo na SME ou em outro órgão?',
                    ],
                    'acumula_descricao' => [
                        'label' => 'Informe o órgão e o cargo onde acumula',
                    ],
                ],

            ],

        ],

    ],

    'formacao' => [

        'titulo' => 'Formação Acadêmica',

        'subsecoes' => [

            'escolaridade' => [

                'titulo' => 'Escolaridade',
                'campos' => [
                    'escolaridade' => [
                        'label' => 'Qual seu nível de escolaridade?',
                    ],
                ],

            ],

            'graduacao' => [

                'titulo' => 'Bacharelado, Tecnólogo, Licenciatura',
                'campos' => [
                    'curso_graduacao' => [
                        'label' => 'Curso de graduação (nome do curso e da instituição de ensino)',
                    ],
                    'ano_conclusao' => [
                        'label' => 'Ano de conclusão ou previsão de concluir',
                    ],
                    'outra_graduacao' => [
                        'label' => 'Possui outra graduação e gostaria de informar?',
                    ],
                    'segunda_graduacao' => [
                        'label' => 'Segunda graduação (nome do curso e da instituição de ensino)',
                    ],
                    'ano_conclusao_seg' => [
                        'label' => 'Ano de conclusão ou previsão de concluir',
                    ],
                ],

            ],

            'outros_cursos' => [

                'titulo' => 'Outros Cursos e/ou Projetos Relevantes',
                'campos' => [
                    'outros_cursos' => [
                        'label' => 'Por favor, descreva outros cursos e/ou projetos relevantes',
                    ],
                ],

            ],

        ],

    ],

    'vivencias' => [

        'titulo' => 'Vivências Profissionais',

        'subsecoes' => [

            'vivencia' => [

                'titulo' => 'Vivência', // Será concatenado com o número
                'campos' => [
                    'organizacao_empresa' => [
                        'label' => 'Organização / Empresa',
                    ],
                    'cargo_funcao' => [
                        'label' => 'Cargo / Função',
                    ],
                    'duracao' => [
                        'label' => 'Duração',
                    ],
                    'atividades_competencias' => [
                        'label' => 'Atividades e Competências Desenvolvidas',
                    ],
                ],

            ],

        ],

        'mensagem_vazia' => 'Nenhuma vivência profissional cadastrada.',

    ],

    'tecnologia' => [

        'titulo' => 'Conhecimentos em Informática e Tecnologia',
        
        'descricao' => 'Quais dos sistemas a seguir você já trabalhou e/ou tem facilidade de navegação?',

        'subsecoes' => [

            'competencias' => [

                'titulo' => 'Sistemas e Competências',
                'campos' => [
                    'sistema' => [
                        'label' => 'Sistema',
                    ],
                    'nivel' => [
                        'label' => 'Nível',
                    ],
                ],

            ],

        ],

    ],

    'comportamental' => [

        'titulo' => 'Preferências e Perfil Comportamental',
        
        'descricao' => 'Por gentileza, indique a ação que mais reflete sua atitude nas situações a seguir.',

        'subsecoes' => [

            'perfil' => [

                'titulo' => 'Perfil Comportamental',
                'campos' => [
                    'afirmacao' => [
                        'label' => 'Afirmação',
                    ],
                    'acao' => [
                        'label' => 'Ação',
                    ],
                ],

            ],

        ],

    ],

    'visualizacao' => [
        'titulo' => 'Quem poderá visualizar as informações que você preencheu neste cadastro?',
        'sugestoes' => 'Sugestões',
    ],

    'etapa' => [

        'titulo' => 'Etapa do Processo',

    ]

];