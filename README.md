# Portal Acadêmico

## Descrição do Projeto

O **Portal Acadêmico** é uma aplicação web desenvolvida para gerenciar e facilitar as interações entre alunos, professores e a secretaria de uma instituição de ensino. Ele oferece um conjunto abrangente de funcionalidades para otimizar processos acadêmicos e administrativos, proporcionando uma experiência integrada para todos os usuários.

## Funcionalidades

O sistema é dividido em módulos principais, cada um atendendo a um perfil de usuário específico:

### Módulo Aluno

*   **Atendimento e-Ticket:** Sistema de tickets para solicitações e suporte.
*   **Boletim:** Visualização de notas e desempenho acadêmico.
*   **Cadastro Wi-Fi:** Gerenciamento de acesso à rede Wi-Fi da instituição.
*   **Carteirinha Escolar:** Geração e acesso à carteirinha estudantil.
*   **Carteirinha Veículos:** Cadastro de veículos para acesso ao campus.
*   **Contrato Financeiro:** Consulta de informações financeiras e contratos.
*   **Atividade Complementar:** Registro e acompanhamento de atividades complementares.
*   **Estágio:** Gerenciamento de informações e documentos relacionados a estágios.
*   **Meu Cadastro:** Edição e consulta de dados cadastrais pessoais.
*   **Meu Curso:** Informações detalhadas sobre o curso atual do aluno.
*   **Minhas Disciplinas:** Acesso a informações e materiais das disciplinas matriculadas.
*   **NAP (Núcleo de Apoio Psicopedagógico):** Agendamento e acompanhamento de atendimentos psicopedagógicos.
*   **Requerimento Bolsa:** Processo de solicitação e acompanhamento de bolsas de estudo, incluindo editais e tutoriais.

### Módulo Professor

*   **Atendimento e-Ticket:** Sistema de tickets para solicitações e suporte.
*   **Cadastro Wi-Fi:** Gerenciamento de acesso à rede Wi-Fi da instituição.
*   **Carteirinha Veículos:** Cadastro de veículos para acesso ao campus.
*   **Agendar Equipamento:** Agendamento de equipamentos e recursos da instituição.
*   **Plano de Ensino:** Cadastro e consulta de planos de ensino das disciplinas.
*   **Diário de Classe:** Gerenciamento de frequência e conteúdo das aulas.
*   **Enviar Provas e Anexos:** Upload de provas e materiais complementares.
*   **Lançamento de Notas:** Registro e edição de notas dos alunos.
*   **Meu Cadastro:** Edição e consulta de dados cadastrais pessoais.
*   **Meu Curso:** Informações sobre os cursos e turmas lecionadas.
*   **Minhas Disciplinas:** Acesso a informações e materiais das disciplinas lecionadas.
*   **Solicitação Marketing:** Requisição de materiais e suporte ao departamento de marketing.

### Módulo Secretaria

*   **Gestão Acadêmica:**
    *   **Cadastros:** Gerenciamento de alunos, professores e responsáveis.
    *   **Matrículas:** Processo de matrícula e rematrícula.
    *   **Gerar Carteirinhas:** Emissão de carteirinhas estudantis.
    *   **Turmas e Horários:** Cadastro de turmas, horários de aula e gerenciamento de atribuições.
    *   **Parâmetros:** Configurações de provas integradas, núcleo integrador, sistema de avaliação e datas de apontamentos bimestrais.
*   **Atendimento:** Listagem geral e gerenciamento de tickets.
*   **Carteirinha Veículos:** Cadastro, análise e listagem de veículos.
*   **Equipamentos:** Agendamentos e cadastros de equipamentos.
*   **Atividade Complementar:** Gerenciamento e validação de atividades complementares.
*   **Estágio:** Gerenciamento de informações e documentos relacionados a estágios.
*   **Equivalência:** Gestão de equivalências de disciplinas.
*   **Plano de Ensino:** Gerenciamento de planos de ensino.
*   **Conteúdo Programático:** Cadastro e consulta de conteúdo programático.
*   **Enviar Provas e Anexos:** Gerenciamento de provas e anexos.
*   **Enviar Email Turma:** Envio de e-mails para turmas específicas.
*   **Aviso Página Inicial:** Publicação de notícias e avisos na página inicial.
*   **Meu Curso:** Informações sobre os cursos da instituição.
*   **NAP (Núcleo de Apoio Psicopedagógico):** Cadastro de datas e listagem de atendimentos.
*   **Secretaria Digital:**
    *   **Configurações:** Gerenciamento de versões XSD, categorias de atividades complementares e etiquetas.
    *   **Cadastros:** Gerenciamento de mantenedoras, emissoras, cursos e alunos para diplomas digitais.

## Tecnologias Utilizadas

Este projeto é construído sobre o **Adianti Framework**, uma plataforma de desenvolvimento rápido de aplicações (RAD) para PHP. As principais dependências incluem:

*   **PHP:** Linguagem de programação principal.
*   **PHPMailer:** Biblioteca para envio de e-mails.
*   **pQuery:** Ferramenta para manipulação de HTML/XML.
*   **php-barcode-generator:** Geração de códigos de barras.
*   **Dompdf:** Geração de PDFs a partir de HTML.
*   **BaconQrCode:** Geração de códigos QR.
*   **firebase/php-jwt:** Implementação de JSON Web Tokens (JWT).
*   **linfo:** Biblioteca para informações do sistema.
*   **adianti/plugins, adianti/pdfdesigner, adianti/barcode-document, adianti/html-document, adianti/studio-forms, adianti/table-writers:** Componentes específicos do Adianti Framework para diversas funcionalidades.
*   **pablodalloglio/ole, pablodalloglio/spreadsheet_excel_writer, pablodalloglio/fpdf, pablodalloglio/phprtflite:** Bibliotecas para manipulação de documentos (Excel, PDF, RTF).
*   **spomky-labs/otphp:** Implementação de senhas de uso único baseadas em tempo (TOTP) e contador (HOTP).
*   **jfcherng/php-diff:** Biblioteca para comparação de diferenças entre textos.

## Instalação

Para configurar o ambiente de desenvolvimento e executar o projeto localmente, siga os passos abaixo:

1.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/SofiaAraki/template.git
    cd template
    ```

2.  **Instalar Dependências:**
    Certifique-se de ter o Composer instalado. Em seguida, execute:
    ```bash
    composer install
    ```

3.  **Configuração do Banco de Dados:**
    *   Crie um banco de dados MySQL/PostgreSQL (ou outro compatível) para o projeto.
    *   Edite o arquivo de configuração do banco de dados (geralmente em `app/config/unit_database.php` ou similar, dependendo da configuração do Adianti) com suas credenciais.

4.  **Configuração do Servidor Web:**
    *   Configure um servidor web (Apache ou Nginx) para apontar para o diretório `web` (ou `public`) do projeto.
    *   Certifique-se de que as permissões de escrita estejam corretas para os diretórios `tmp/` e `files/`.

5.  **Acessar a Aplicação:**
    *   Abra seu navegador e acesse a URL configurada para o projeto.
    *   Siga as instruções do Adianti Framework para a configuração inicial, se necessário.

## Uso

Após a instalação, a aplicação pode ser acessada via navegador. Os usuários (alunos, professores, secretaria) podem fazer login com suas credenciais para acessar as funcionalidades específicas de seus perfis.

## Contribuição

Contribuições são bem-vindas! Para contribuir com o projeto, siga os passos:

1.  Faça um fork do repositório.
2.  Crie uma nova branch para sua feature (`git checkout -b feature/minha-nova-feature`).
3.  Faça suas alterações e commit-as (`git commit -am 'Adiciona nova feature'`).
4.  Envie para a branch original (`git push origin feature/minha-nova-feature`).
5.  Abra um Pull Request.

## Licença

Este projeto está licenciado sob a licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.

## Referências

*   [Adianti Framework](http://www.adianti.com.br/framework) - Site oficial do Adianti Framework.
*   [Composer](https://getcomposer.org/) - Gerenciador de dependências para PHP.
