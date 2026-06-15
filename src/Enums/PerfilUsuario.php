<?php

namespace App\Enums;

enum PerfilUsuario: string
{
    case ADMIN = 'ADMIN';
    case MEDICO = 'MEDICO';
    case ENFERMEIRO = 'ENFERMEIRO';
    case RECEPCAO = 'RECEPCAO';
    case USUARIO = 'USUARIO';
}