<?php
/******************************************************/
	/*	Form per l'inserimento dei metadati	      */
	/******************************************************/

	$id = $this->current->coursemodule;

	//ViewPreviouslyInsertedMetadata
	$sql="SELECT count(*) FROM Sql973959_3.mdl_metadata WHERE id_resource = '".$id."'";
	$num_rows = $DB->count_records_sql($sql);

	if($num_rows > 0) {
		$mform->addElement('header','View Previously Inserted Metadata', convert_metadata('vpim'));

		$sql="SELECT id_metadata, property, value FROM Sql973959_3.mdl_metadata WHERE id_resource = '".$id."' AND id_resource IS NOT NULL AND id_course IS NOT NULL AND id_course_sections IS NOT NULL";
		$fields = $DB->get_records_sql($sql);
		$choices = array();
		$i = 0;
		foreach($fields as $field) {
			$choices[$i] = convert_metadata($field->property).": ".translate_language(translate_format(translate_type(translate_time($field->value))));
			$i++;
		}
		$select = $mform->addElement('select', 'CheckMetadata', convert_metadata('cm'), $choices, array('style'=>'min-height:130px;'));
		$select->setMultiple(true);
	}

	$mform->addElement('header','Metadata', convert_metadata('metadata'));

	$missing = array(
    		"missing1" => convert_metadata("missLang"),
		"missing2" => convert_metadata("missFormat"),
		"missing3" => convert_metadata("missLRT"),
		"missing4" => convert_metadata("missTLT")
	);

	//Language
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'language'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_language($field->value);
		$i++;
	}
        $mform->addElement('select', 'Language', convert_metadata('language'), $choices);
	$mform->addRule('Language', $missing['missing1'], 'required', null, 'client'); 

	//Keywords
	$mform->addElement('text','Keywords', convert_metadata('ks'),'id="text-area" class="text_area" maxlength="254" size="50"');
	$mform->setType('Keywords', PARAM_NOTAGS); 


	//Format
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'format'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_format($field->value);
		$i++;
	}
        $mform->addElement('select', 'Format', convert_metadata('format'), $choices);
	$mform->addRule('Format', $missing['missing2'], 'required', null, 'client');

	//LearningResourceType
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_type($field->value);
		$i++;
	}
        $mform->addElement('select', 'LearningResourceType', convert_metadata('resourcetype'), $choices);
	$mform->addRule('LearningResourceType', $missing['missing3'], 'required', null, 'client');

	//TypicalLearningTime
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'time'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_time($field->value);
		$i++;
	}
        $mform->addElement('select', 'TypicalLearningTime', convert_metadata('time'), $choices);
	$mform->addRule('TypicalLearningTime', $missing['missing4'], 'required', null, 'client');

	/******************************************************/
	/*			FINE	      		      */
	/******************************************************/
	?>