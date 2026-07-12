<?php
class FiGradeDisciplinaEtapaFrente extends TRecord
{
    const TABLENAME  = 'FI_GradeDisciplinaEtapa_Frente';
    const PRIMARYKEY = 'CodGradeDisciplinaEtapa_Frente'; // Chave primária exata do banco
    const IDPOLICY   = 'serial'; 

    private $grade_disciplina_etapa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Mapeamento dos atributos idênticos às colunas reais do SQL Server/MySQL
        parent::addAttribute('CodGradeDisciplinaEtapa');
        parent::addAttribute('NomeFrente');
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('CargaHorariaSemanal_Parcial');
    }

    /**
     * Relacionamento com a tabela pai: FI_GradeDisciplinaEtapa
     * Permite acessar as informações da disciplina master da etapa
     * Exemplo: $this->grade_disciplina_etapa->NomeDisciplina (dependendo das suas colunas na outra tabela)
     */
    public function get_grade_disciplina_etapa()
    {
        if (empty($this->grade_disciplina_etapa))
        {
            // Substitua 'FI_GradeDisciplinaEtapa' pelo nome da classe Model correspondente se ela usar outra nomenclatura
            $this->grade_disciplina_etapa = new FiGradeDisciplinaEtapa($this->CodGradeDisciplinaEtapa);
        }
        return $this->grade_disciplina_etapa;
    }

    /**
     * Propriedade virtual amigável para retornar o nome da disciplina/frente.
     * Caso queira usar um fallback direto no código para simplificar a busca por nomes no seu Form.
     */
    public function get_NomeDisciplina()
    {
        return $this->NomeFrente;
    }
}