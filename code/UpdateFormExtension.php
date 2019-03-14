<?php

class sgn_hasoneedit_UpdateFormExtension extends Extension {

    /**
     * @param Form $form
     */
    public function updateItemEditForm(Form $form)
    {
        $this->updateEditForm($form);
    }

	/**
	 * @param Form $form
	 */
	public function updateEditForm(Form $form)
	{
		$record = $form->getRecord();
        $fieldList = $form->Fields();

		foreach ($fieldList->dataFields() as $name => $field) {
            $name = HasOneEdit::normaliseSeparator($name);
            if (!HasOneEdit::isHasOneEditField($name)) continue;

			$field->setName($name);

            if ($field instanceof UploadField) {
                $field = HasOneUploadField::create($field);
                $fieldList->replaceField($name, $field);
            }

            // Skip populating value if record doesn't exist yet, or field already has value
            if (!$record || $field->Value()) continue;

            list($relationName, $fieldOnRelation) = HasOneEdit::getRelationNameAndField($name);
            $relatedObject = HasOneEdit::getRelationRecord($record, $relationName);
            if ($relatedObject === null) continue;

            if ($field instanceof HasOneUploadField) {
                if ($relatedObject->hasField("{$fieldOnRelation}ID")) {
                    $field->setValue([ 'Files' => [ $relatedObject->getField("{$fieldOnRelation}ID") ] ]);
                }

            } else {
                if ($relatedObject->hasField($fieldOnRelation)) {
                    $field->setValue($relatedObject->getField($fieldOnRelation));
                }
            }

		}
	}

}
