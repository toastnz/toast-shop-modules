<?php

/**
 * Class ToastProductExtension
 *
 * @property Product $owner
 */
class ToastProductExtension extends DataExtension
{
    private static $many_many = [
        'RelatedProducts' => 'Product'
    ];

    private static $many_many_extraFields = [
        'RelatedProducts' => [
            'SortOrder' => 'Int'
        ]
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $config = GridFieldConfig_RelationEditor::create(50);
        $config->addComponent(GridFieldOrderableRows::create('SortOrder'))
            ->removeComponentsByType('GridFieldAddNewButton');

        $gridField = GridField::create(
            'RelatedProducts',
            'Related Products',
            $this->owner->RelatedProducts(),
            $config
        );

        $fields->addFieldsToTab('Root.RelatedProducts', [
            $gridField
        ]);
    }
}
