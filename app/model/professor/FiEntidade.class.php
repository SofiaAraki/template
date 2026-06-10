<?php
/**
 * FiEntidade Active Record
 * @author  <your-name-here>
 */
class FiEntidade extends TRecord
{
    const TABLENAME = 'FI_Entidade';
    const PRIMARYKEY= 'CodEntidade';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NomeFantasia');
        parent::addAttribute('RazaoSocial');
        parent::addAttribute('Endereco');
        parent::addAttribute('Bairro');
        parent::addAttribute('CEP');
        parent::addAttribute('Telefone');
        parent::addAttribute('EMail');
        parent::addAttribute('CNPJ');
        parent::addAttribute('InscricaoEstadual');
        parent::addAttribute('CodCidade');
        parent::addAttribute('Logotipo');
        parent::addAttribute('CF_DiaVencimento');
        parent::addAttribute('LoginInicial');
        parent::addAttribute('Req_Nome');
        parent::addAttribute('Req_Nacionalidade');
        parent::addAttribute('Req_EstadoCivil');
        parent::addAttribute('Req_Profissao');
        parent::addAttribute('Req_Nascimento');
        parent::addAttribute('Req_Rg');
        parent::addAttribute('Req_CPF');
        parent::addAttribute('Req_Endereco');
        parent::addAttribute('Req_EnderecoNumero');
        parent::addAttribute('Req_Cidade');
        parent::addAttribute('Req_UF');
        parent::addAttribute('Req_CEP');
        parent::addAttribute('Req_Telefone');
        parent::addAttribute('Req_Linha1');
        parent::addAttribute('Req_Linha2');
        parent::addAttribute('Req_Linha3');
        parent::addAttribute('Req_Ciente1');
        parent::addAttribute('Req_Ciente2');
        parent::addAttribute('Req_Ciente3');
        parent::addAttribute('Req_Ciente4');
        parent::addAttribute('Req_Ciente5');
        parent::addAttribute('Req_Ciente6');
        parent::addAttribute('Req_Ciente7');
        parent::addAttribute('Req_Ciente8');
        parent::addAttribute('Req_Ciente9');
        parent::addAttribute('Req_Ciente10');
        parent::addAttribute('Req_Ciente11');
        parent::addAttribute('Req_Ciente12');
        parent::addAttribute('Req_Ciente13');
        parent::addAttribute('Host');
        parent::addAttribute('Porta');
        parent::addAttribute('UserID');
        parent::addAttribute('Remetente_Name');
        parent::addAttribute('Remetente_email');
        parent::addAttribute('Mensagem1');
        parent::addAttribute('Mensagem2');
        parent::addAttribute('Mensagem3');
        parent::addAttribute('Mensagem4');
        parent::addAttribute('Mensagem5');
        parent::addAttribute('Mensagem6');
        parent::addAttribute('Mensagem7');
        parent::addAttribute('Mensagem8');
        parent::addAttribute('Mensagem9');
        parent::addAttribute('Mensagem10');
        parent::addAttribute('nomesecretario');
        parent::addAttribute('URLNotasFaltas');
        parent::addAttribute('URLBoletim');
        parent::addAttribute('URLEntidade');
        parent::addAttribute('HISTORICO_CAB1');
        parent::addAttribute('HISTORICO_CAB2');
        parent::addAttribute('HISTORICO_CAB3');
        parent::addAttribute('SECRETARIO_DADOS1');
        parent::addAttribute('SECRETARIO_DADOS2');
        parent::addAttribute('SECRETARIO_DADOS3');
        parent::addAttribute('SECRETARIO_DADOS4');
        parent::addAttribute('DIRETOR_DADOS1');
        parent::addAttribute('DIRETOR_DADOS2');
        parent::addAttribute('DIRETOR_DADOS3');
        parent::addAttribute('DIRETOR_DADOS4');
        parent::addAttribute('ImpMatNaoConf');
        parent::addAttribute('OrdemListas');
        parent::addAttribute('DiarioImpDisp');
        parent::addAttribute('BloqueioMatriculas');
        parent::addAttribute('COMPLEMENTO');
        parent::addAttribute('LogoTipoImagem');
        parent::addAttribute('HISTORICO_CAB4');
        parent::addAttribute('PrefixoREP');
        parent::addAttribute('ATA_Obs1');
        parent::addAttribute('ATA_Obs2');
        parent::addAttribute('ATA_Cab1');
        parent::addAttribute('ATA_Cab2');
        parent::addAttribute('EnsinoMedioRequerido');
        parent::addAttribute('TransicaoCurso');
        parent::addAttribute('DocsRequerido');
        parent::addAttribute('Req_obs1');
        parent::addAttribute('Req_obs2');
        parent::addAttribute('Req_obs3');
        parent::addAttribute('LEGENDA_DESISTENTE');
        parent::addAttribute('Cartao_Plano2Frente');
        parent::addAttribute('Cartao_Plano2Verso');
        parent::addAttribute('CabecalhoImagem');
        parent::addAttribute('RodapeImagem');
        parent::addAttribute('Historico_DefEtapa');
    }


}
