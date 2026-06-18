<?php
class MeuCursoInformativo extends TPage
{
    private $form;   

    public function __construct($param)
    {
        parent::__construct();
        
        TTransaction::open('Felabs_DB');
        
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        $meuCursoInfo = new MeuCurso($param['key']);
        
        // Inicializa o formulário padrão do Adianti
        $this->form = new BootstrapFormBuilder('form_search_AtividadeAluno');
        $this->form->setFormTitle($meuCursoInfo->nome);
        
        // 1. EXIBIÇÃO DA DESCRIÇÃO (Totalmente blindada contra exposição de código e compatível com Dark Mode)
        if(!empty($meuCursoInfo->descricao)) 
        {                    
            $descricaoLimpa = $meuCursoInfo->descricao;

            // Passo 1: Remover completamente tags <font> e seus atributos antes de qualquer processamento
            $descricaoLimpa = preg_replace('/<font[^>]*>/i', '', $descricaoLimpa);
            $descricaoLimpa = str_ireplace('</font>', '', $descricaoLimpa);

            // Passo 2: Limpar estilos inline, cores e famílias de fontes residuais
            $descricaoLimpa = preg_replace('/style=("|\')(.*?)("|\')/i', '', $descricaoLimpa);
            $descricaoLimpa = preg_replace('/color\s*:\s*[^;"]+;?/i', '', $descricaoLimpa);
            $descricaoLimpa = preg_replace('/font-family\s*:\s*[^;"]+;?/i', '', $descricaoLimpa);
            $descricaoLimpa = preg_replace('/face=("|\')(.*?)("|\')/i', '', $descricaoLimpa);
            
            // Passo 3: Limpeza fina de aspas ou atributos soltos vindos do WYSIWYG
            $descricaoLimpa = str_ireplace(['color="#333333"', 'color="black"', 'color="#000000"', '"#323232"'], '', $descricaoLimpa);

            // Criamos o container nativo usando classes utilitárias do Bootstrap (text-body garante a cor automática do tema)
            $descRender = new TElement('div');
            $descRender->class = 'text-body p-2'; 
            $descRender->style = 'font-size: 16px; line-height: 1.8; margin: 10px 0; width: 100%; word-break: break-word;';
            
            // Adiciona o HTML perfeitamente limpo
            $descRender->add($descricaoLimpa);
            
            $this->form->addFields([$descRender]);
        }

        // 2. SEÇÃO DE ARQUIVO ANEXO
        if(!empty($meuCursoInfo->filename)) 
        {  
            $labelAnexo = new TLabel('Arquivo complementar disponível:', '', '', 'B');
            
            $button = new TButton('download_anexo');
            $button->setImage('fas:cloud-download-alt white');
            $button->setLabel('Baixar Anexo');
            $button->class = 'btn btn-success';
            $button->setAction(new TAction(array($this, 'onDownloadMaster'), $param), 'Download');
            
            $row = $this->form->addFields([$labelAnexo], [$button]);
            $row->layout = ['col-sm-4', 'col-sm-8'];
            $row->style = 'margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(128,128,128,0.2);';
        }

        $param['curso'] = $meuCursoInfo->curso_id;

        // 3. AÇÕES DO FORMULÁRIO (Botão padrão no rodapé)
        if($user->funcao_legado == 'Aluno')
        {
            $this->form->addAction('Voltar para Meu Curso', new TAction(array('MeuCursoViewAluno', 'verPagina'), $param), 'fa:arrow-left');
        }
        elseif($user->funcao_legado == 'Professor')
        {
            $this->form->addAction('Voltar para Meu Curso', new TAction(array('MeuCursoViewProfessor', 'verPagina'), $param), 'fa:arrow-left');
        }
        else
        {
            $this->form->addAction('Voltar para Meu Curso', new TAction(array('MeuCursoView', 'verPagina'), $param), 'fa:arrow-left');
        }

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);
        
        TTransaction::close();
    }

    public function onDownloadMaster($param)
    {
        try
        {              
            $id = $param['id'];  
    
            TTransaction::open('Felabs_DB'); 
            $object = new MeuCurso($id); 
            TTransaction::close(); 

            if(!empty($object->filename))
            {              
                if (strtolower(substr($object->filename, -4)) == 'html')
                {
                    $win = TWindow::create($object->filename, 0.8, 0.8);
                    $win->add(file_get_contents("files/meucurso/".$object->filename));
                    $win->show();
                }
                else
                {
                    TPage::openFile("files/meucurso/".$object->filename);
                }
                                  
                $parametros = [];
                $parametros['id'] = $param['id'];
                $parametros['key'] = $param['id'];

                TApplication::loadPage('MeuCursoInformativo', 'mostrar', $parametros);
            }
            else
            {
                new TMessage('info', 'Este tópico não possui anexos'); 
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }

    public function mostrar($param)
    {
    }
}