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
            self::A_PROSPECTAR => 'border-blue-200 bg-blue-50 text-blue-700 font-medium',
            self::CONTATO_INICIADO => 'border-indigo-200 bg-indigo-50 text-indigo-700 font-medium',
            self::AGUARDANDO_RESPOSTA => 'border-amber-200 bg-amber-50 text-amber-800 font-medium',
            self::EM_NEGOCIACAO => 'border-purple-200 bg-purple-50 text-purple-700 font-medium',
            self::PROPOSTA_ENVIADA => 'border-sky-200 bg-sky-50 text-sky-700 font-medium',
            self::CLIENTE_FECHADO => 'border-emerald-200 bg-emerald-50 text-emerald-700 font-medium',
            self::NAO_INTERESSADO => 'border-red-200 bg-red-50 text-red-700 font-medium',
            self::SEM_RETORNO => 'border-orange-200 bg-orange-50 text-orange-700 font-medium',
            self::ARQUIVADO => 'border-zinc-200 bg-zinc-100 text-zinc-600 font-medium',
        };
    }

    public function topBorderColor(): string
    {
        return match ($this) {
            self::A_PROSPECTAR => 'border-t-blue-500',
            self::CONTATO_INICIADO => 'border-t-indigo-500',
            self::AGUARDANDO_RESPOSTA => 'border-t-amber-500',
            self::EM_NEGOCIACAO => 'border-t-purple-500',
            self::PROPOSTA_ENVIADA => 'border-t-sky-500',
            self::CLIENTE_FECHADO => 'border-t-emerald-500',
            self::NAO_INTERESSADO => 'border-t-red-500',
            self::SEM_RETORNO => 'border-t-orange-500',
            self::ARQUIVADO => 'border-t-zinc-400',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::A_PROSPECTAR => 'bg-blue-500',
            self::CONTATO_INICIADO => 'bg-indigo-500',
            self::AGUARDANDO_RESPOSTA => 'bg-amber-500',
            self::EM_NEGOCIACAO => 'bg-purple-500',
            self::PROPOSTA_ENVIADA => 'bg-sky-500',
            self::CLIENTE_FECHADO => 'bg-emerald-500',
            self::NAO_INTERESSADO => 'bg-red-500',
            self::SEM_RETORNO => 'bg-orange-500',
            self::ARQUIVADO => 'bg-zinc-400',
        };
    }
}
