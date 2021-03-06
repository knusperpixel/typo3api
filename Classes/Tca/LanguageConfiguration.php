<?php

namespace Typo3Api\Tca;


class LanguageConfiguration implements TcaConfigurationInterface, DefaultTabInterface
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        $ctrl['languageField'] = 'sys_language_uid';
        $ctrl['translationSource'] = 'l10n_source';
        $ctrl['transOrigPointerField'] = 'l18n_parent';
        $ctrl['transOrigDiffSourceField'] = 'l18n_diffsource';
    }

    public function getColumns(string $tableName): array
    {
        return [
            'sys_language_uid' => $GLOBALS['TCA']['tt_content']['columns']['sys_language_uid'],
            'l10n_source' => $GLOBALS['TCA']['tt_content']['columns']['l10n_source'],
            'l18n_diffsource' => $GLOBALS['TCA']['tt_content']['columns']['l18n_diffsource'],
            'l18n_parent' => [
                'exclude' => true,
                'displayCond' => 'FIELD:sys_language_uid:>:0',
                'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', 0]
                    ],
                    'foreign_table' => $tableName,
                    'foreign_table_where' => "AND $tableName.pid=###CURRENT_PID### AND $tableName.sys_language_uid IN (-1,0)",
                    'default' => 0
                ]
            ],
            // TODO, wrong localization fieldnames, what is l18n supposed to mean?
            // l10n = localization
            // i18n = internationalization
            // l18n = ?
            // it should be l10n like with the source field but that would be a breaking change for a future release
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [
            'language' => [
                'showitem' => implode(', ', [
                    'sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel',
                    'l18n_parent'
                ])
            ]
        ];
    }

    public function getShowItemString(string $tableName): string
    {
        return "--palette--;;language";
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [
            $tableName => [
                "sys_language_uid int(11) DEFAULT '0' NOT NULL",
                "l10n_source int(11) DEFAULT '0' NOT NULL",
                "l18n_diffsource mediumtext",
                "l18n_parent int(11) DEFAULT '0' NOT NULL",
                "KEY language (l18n_parent, sys_language_uid)",
            ]
        ];
    }

    public function getDefaultTab(): string
    {
        return 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language';
    }

}