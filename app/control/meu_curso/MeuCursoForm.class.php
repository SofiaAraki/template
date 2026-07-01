<?php

class MeuCursoForm extends TPage
{
    protected $form; 
    
    public function __construct($param)
    {
        parent::__construct();     
        
        // Instancia o formulário utilizando Bootstrap direto para melhor performance visual
        //$this->form = new BootstrapFormBuilder('form_MeuCurso');

        // Remove o TQuickForm, o class = tform e o BootstrapFormWrapper. Use apenas:
        $this->form = new BootstrapFormBuilder('form_MeuCurso');
        $this->form->style = 'width:100%';

        // O próprio construtor gerencia o título principal do painel externo
        $this->form->setFormTitle('Adicionar Arquivo'); 

        if (isset($param['method']) && $param['method'] == 'mostrarInfo') {
            $this->form->setFormTitle('Adicionar Informativo');
        }
        
        // Criação dos campos do formulário
        $id         = new THidden('id');
        $curso_id   = new THidden('curso_id');
        $filename   = new TFile('filename');
        $nome       = new TEntry('nome');
        $descricao  = new THtmlEditor('descricao');
        $tipo       = new TCombo('tipo');
        $data_reg   = new THidden('data_reg');
        
        // Definição dos itens do Combo Tipo
        $tipoItems = [
            'A' => 'Atividades Complementares',
            'C' => 'Calendários',
            'E' => 'Estágio Supervisionado',
            'G' => 'Grade Curricular',
            'H' => 'Horário de Aulas',
            'I' => 'Informativo',
            'O' => 'Outros',
            'P' => 'Projeto Pedagógico do Curso',
            'T' => 'Trabalho de Conclusão de Curso (TCC)'
        ];
        $tipo->addItems($tipoItems);
        
        $descricao->setSize('100%', 150);     

        // Definição segura do valor inicial do curso_id
        if (isset($param['curso'])) {
            $curso_id->setValue((int)$param['curso']);
        } elseif (TSession::getValue('cursoid')) {
            $curso_id->setValue((int)TSession::getValue('cursoid'));
        }

        // Verifica o método de abertura para preencher o tipo automaticamente
        $method = isset($param['method']) ? $param['method'] : '';
        if ($method == 'mostrarInfo') {
            $tipo->setValue('I');
        }
       
        // Adiciona os campos ao formulário de forma organizada
        $this->form->addFields([$id], [$curso_id], [$data_reg]); // Escondidos em linha técnica
        $this->form->addFields([new TLabel('Nome:')], [$nome]);
        $this->form->addFields([new TLabel('Tipo:')], [$tipo]);
        $this->form->addFields([new TLabel('Descrição:')], [$descricao]);
        $this->form->addFields([new TLabel('Arquivo anexo:')], [$filename]);

        // Validações obrigatórias
        $nome->addValidation('Nome', new TRequiredValidator);
        $tipo->addValidation('Tipo', new TRequiredValidator);

        // Configuração das ações (Botões)       
        $this->form->addAction('Voltar', new TAction([$this, 'onBack']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');

        if ($method == 'onEdit' || isset($param['key'])) {
            $this->form->addAction('Excluir', new TAction([$this, 'onDelete'], $param), 'far:trash-alt red');
        }   
        
        // Container de exibição da página
        $container = new TVBox;
        $container->style = 'width: 100%';

        $container->add($this->form);

        // if ($method == 'mostrarInfo') {
        //     $container->add(TPanelGroup::pack('Adicionar Informativo', $this->form));
        // } else {
        //     $container->add(TPanelGroup::pack('Adicionar Arquivo', $this->form));
        // }
        
        parent::add($container);
    }

    public function onBack()
    {       
        $parametro = [];
        $parametro['curso'] = TSession::getValue('cursoid');
        
        TApplication::loadPage('MeuCursoView', 'verPagina', $parametro);
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $this->form->validate(); 
            
            $object = new MeuCurso;  
            $data = $this->form->getData(); 

            if ($data->tipo != 'I' && empty($data->filename)) {
                throw new Exception('O campo Anexo está vazio');
            }

            $data->data_reg = date('Y-m-d H:i:s');

            // Recupera o ID do usuário logado na sessão para evitar erro de variável nula
            $userId = TSession::getValue('userid');

            if ($data->id) {
                $testa = new MeuCurso($data->id);
            }
     
            if ($data->filename) {
                if (!empty($data->id) && isset($testa)) {
                    // Se o arquivo enviado for diferente do antigo, processa a alteração
                    if ($data->filename != $testa->filename) {  
                        $today = date("YmdHis");
                        $source_file = 'tmp/'.$data->filename;
                        $nomeArquivo = 'meucurso_'.$today.'_'.$userId.'_'.$data->filename;
                        $target_file = 'files/meucurso/'.$nomeArquivo;
            
                        if (file_exists($source_file)) {
                            rename($source_file, $target_file);
                            $data->filename = $nomeArquivo;
                        }
                    }
                } else {
                    // Novo registro com upload de arquivo
                    $today = date("YmdHis");
                    $source_file = 'tmp/'.$data->filename;
                    $nomeArquivo = 'meucurso_'.$today.'_'.$userId.'_'.$data->filename;
                    $target_file = 'files/meucurso/'.$nomeArquivo;
            
                    if (file_exists($source_file)) {
                        rename($source_file, $target_file);
                        $data->filename = $nomeArquivo;
                    }
                }
            }

            $object->fromArray((array) $data); 
            $object->store(); 

            $data->id = $object->id;
            $this->form->setData($data); 
            
            TTransaction::close(); 
            
            $parametro = [];
            $parametro['curso'] = TSession::getValue('cursoid');

            new TMessage('info', 'Registro salvo com sucesso!', TAction::newFromMethod('MeuCursoView', 'verPagina', $parametro)); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData($this->form->getData()); 
            TTransaction::rollback(); 
        }
    }

    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new MeuCurso($key); 
                $this->form->setData($object); 
                
                TTransaction::close(); 
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new MeuCurso($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close();
            
            $parametro = [];
            $parametro['curso'] = TSession::getValue('cursoid');

            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), TAction::newFromMethod('MeuCursoView', 'verPagina', $parametro));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }

    public function mostrar($param)
    {
        // Método opcional reservado para lógicas customizadas da view
    }

    public function mostrarInfo($param)
    {
        // Método opcional reservado para lógicas customizadas da view
    }
}