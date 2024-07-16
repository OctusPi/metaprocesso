<?php
namespace App\Models;

class Common{
    public const PRF_ADMIN = 1;
    public const PRF_GESTOR = 2;
    public const PRF_TECNICO = 3;
    public const PRF_AGENTE = 4;
    public const PRF_AUTIDOR = 5;

    public const MOD_INI = ['id' => 0, 'module' => 'dashboard', 'title' => 'Acesso Inicial'];
    public const MOD_MANAGEMENT = ['id' => 1, 'module' => 'management', 'title' => 'Gestão Administrativa'];
    public const MOD_CATALOGS = ['id' => 2, 'module' => 'catalogs', 'title' => 'Catálogos'];
    public const MOD_DFDS = ['id' => 3, 'module' => 'dfds', 'title' => 'DFDs'];
    public const MOD_ETPS = ['id' => 4, 'module' => 'etps', 'title' => 'ETPs'];
    public const MOD_PRICERECORDS = ['id' => 5, 'module' => 'pricerecords', 'title' => 'Registro de Preços'];
    public const MOD_REFTERM = ['id' => 6, 'module' => 'refterms', 'title' => 'Termos de Referência'];
    public const MOD_RISKINESS = ['id' => 7, 'module' => 'riskiness', 'title' => 'Mapas de Risco'];
    public const MOD_PROCCESS = ['id' => 8, 'module' => 'process', 'title' => 'Processos'];
    public const MOD_TRANSMISSION = ['id' => 9, 'module' => 'trasmission', 'title' => 'Publicação e Transmissão'];
    public const MOD_REPORTS = ['id' => 10, 'module' => 'reports', 'title' => 'Relatórios'];
    public const MOD_USERS = ['id' => 11, 'module' => 'users', 'title' => 'Gestão de Usuários'];
    public const MOD_ORGANS = ['id' => 12, 'module' => 'organs', 'title' => 'Gestão de Orgãos'];
    public const MOD_UNITS = ['id' => 13, 'module' => 'units', 'title' => 'Gestão de Unidades'];
    public const MOD_SECTORS = ['id' => 14, 'module' => 'sectors', 'title' => 'Gestão de Setores'];
    public const MOD_ORDINATORS = ['id' => 15, 'module' => 'ordinators', 'title' => 'Gestão de Ordenadores'];
    public const MOD_DEMANDANTS = ['id' => 16, 'module' => 'demandants', 'title' => 'Gestão de Demandantes'];
    public const MOD_COMISSIONS = ['id' => 17, 'module' => 'comissions', 'title' => 'Gestão de Comissões'];
    public const MOD_PROGRAMS = ['id' => 18, 'module' => 'programs', 'title' => 'Gestão de Programas'];
    public const MOD_DOTATIONS = ['id' => 19, 'module' => 'dotations', 'title' => 'Gestão de Dotações'];
    public const MOD_ATTACHMENT = ['id' => 20, 'module' => 'attachment', 'title' => 'Controle de Anexos'];
}