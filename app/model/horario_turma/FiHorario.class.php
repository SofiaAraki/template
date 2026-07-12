<?php
class FiHorario extends TRecord
{
    const TABLENAME  = 'FI_Horario';
    const PRIMARYKEY = 'Codhorario';
    const IDPOLICY   = 'serial'; // Define que o ID (Codhorario) é auto-incremento

    private $turma_etapa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Atributos mapeados exatamente iguais às colunas do seu banco de dados
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodOperador');
        parent::addAttribute('QtdeMaximaAulasPorDia');
        parent::addAttribute('DuracaoAula');
        parent::addAttribute('InicioAula');
        parent::addAttribute('IntervalorAula'); // Nome mantido idêntico ao seu banco (com 'or')
        parent::addAttribute('DuracaoIntervalo');
        parent::addAttribute('Bimestre');
    }

    /**
     * Relacionamento com a Turma/Etapa
     * Permite usar a sintaxe $horario->turma_etapa->NomeTurma na Datagrid
     */
    public function get_turma_etapa()
    {
        // Instancia o objeto associado de forma segura (Lazy Load)
        if (empty($this->turma_etapa))
        {
            // Substitua 'FI_Turma_etapa' pelo nome da classe Model correspondente da sua turma
            $this->turma_etapa = new FiTurmaEtapa($this->CodTurmaetapa);
        }
        return $this->turma_etapa;
    }

    /**
     * Relacionamento Um-para-Muitos (1:N)
     * Método auxiliar para buscar todas as aulas diárias desta grade diretamente
     * Exemplo de uso: $aulas = $horario->getAulasDiarias();
     */
    public function getAulasDiarias()
    {
        return FiHorarioAulasDiarias::where('Codhorario', '=', $this->Codhorario)->load();
    }

    public function get_Operador()
    {
        if (!empty($this->CodOperador)) {
            return new FiOperador($this->CodOperador);
        }
        return '-';
    }
}