<?php

namespace App\Enums;

enum LeadStatus: string
{
    case A_PROSPECTAR = 'a_prospectar';
    case CONTATO_INICIADO = 'contato_iniciado';
    case AGUARDANDO_RESPOSTA = 'aguardando_resposta';
    case EM_NEGOCIACAO = 'em_negociacao';
    case PROPOSTA_ENVIADA = 'proposta_enviada';
    case CLIENTE_FECHADO = 'cliente_fechado';
    case NAO_INTERESSADO = 'nao_interessado';
    case SEM_RETORNO = 'sem_retorno';
    case ARQUIVADO = 'arquivado';

    public function label(): string
    {
        return match ($this) {
            self::A_PROSPECTAR => 'A prospectar',
            self::CONTATO_INICIADO => 'Contato iniciado',
            self::AGUARDANDO_RESPOSTA => 'Aguardando resposta',
            self::EM_NEGOCIACAO => 'Em negociação',
            self::PROPOSTA_ENVIADA => 'Proposta enviada',
            self::CLIENTE_FECHADO => 'Cliente fechado',
            self::NAO_INTERESSADO => 'Não interessado',
            self::SEM_RETORNO => 'Sem retorno',
            self::ARQUIVADO => 'Arquivado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::A_PROSPECTAR => 'border-[#E4E4E7] bg-white text-[#18181B]',
            self::CONTATO_INICIADO => 'border-[#E4E4E7] bg-[#F4F4F5] text-[#18181B] font-medium',
            self::AGUARDANDO_RESPOSTA => 'border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A]',
            self::EM_NEGOCIACAO => 'border-[#18181B] bg-white text-[#18181B] font-medium',
            self::PROPOSTA_ENVIADA => 'border-[#18181B] bg-[#F4F4F5] text-[#18181B] font-medium',
            self::CLIENTE_FECHADO => 'border-black bg-black text-white font-medium',
            self::NAO_INTERESSADO => 'border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A]',
            self::SEM_RETORNO => 'border-[#E4E4E7] bg-[#F4F4F5] text-[#A1A1AA]',
            self::ARQUIVADO => 'border-[#E4E4E7] bg-[#F4F4F5] text-[#A1A1AA]',
        };
    }
}
