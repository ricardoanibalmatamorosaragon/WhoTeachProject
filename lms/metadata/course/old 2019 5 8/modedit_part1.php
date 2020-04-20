<?php
/***********************************************/
	/* Inserimento nel DB di moodle	dei metadati   */
	/***********************************************/

	//MODIFICA DEI METADATI ASSOCIATI A UNA RISORSA

    	$data = $mform->get_data();

	//recupero l'id del corso padre
	$parent_course = $data->course;

	//recupero l'id della sezione padre
	$parent_section = $data->section;
	$sql="SELECT id FROM mdl_course_sections WHERE course = '".$parent_course."' AND section = '".$parent_section."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$parent_section = $field->id;
	}

	//recupero l'id della risorsa
	$module = $data->module;
	$instance = $data->instance;
	$sql="SELECT id AS id_r FROM mdl_course_modules WHERE course = '".$parent_course."' AND section = '".$parent_section."' AND module = '".$module."' AND instance = '".$instance."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$id_resource = $field->id_r;
	}

	//eliminazione dei vecchi metadati 
	$sql="DELETE FROM Sql973959_3.mdl_metadata WHERE id_resource = '".$id_resource."' AND id_course = '".$parent_course."' AND id_course_sections = '".$parent_section."'";
	$DB->execute($sql);

	//Language
	$index0 = $data->Language;
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'language'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections, id_resource, property, value) VALUES ($parent_course, $parent_section, $id_resource, 'language', '".$choices[$index0]."')";
    $DB->execute($sql);
  
	
	//Keywords
	$lang = current_language();
	$index1=$data->Keywords;
	if($index1 != NULL) {
		$pieces = explode(", ", $index1);
		for($i = 0; $i < count($pieces); $i++) {
            		$temp = $pieces[$i];
             		if (($pieces2 = explode (",", $temp)) != false) {
                   		for($j = 0; $j < count($pieces2); $j++) {
                       			$lower = strtolower($pieces2[$j]);
                       			$white_space = trim($lower);
                       			if(!strlen(trim($white_space)) ==0 ) {
                       				$sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections,id_resource, property, value, lang) VALUES ($parent_course, $parent_section,$id_resource, 'keywords', '".$white_space."', '".$lang."')";
                       			$DB->execute($sql);
                       			}
                    		}
             		} 
			else {
                    		$lower = strtolower($temp);
                    		$white_space = trim($lower);
                    		if(!strlen(trim($white_space)) ==0 ) {
                    			$sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections,id_resource, property, value) VALUES ($parent_course, $parent_section,$id_resource, 'keywords', '".$white_space."')";
                    		$DB->execute($sql);
                    		}
            		}
		}
	}

/*Keywords
	$index1=$data->Keywords;
	if($index1 != NULL) {
		$pieces = explode(", ", $index1);
		for($i = 0; $i < count($pieces); $i++) {
			$sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections, id_resource, property, value) VALUES ($parent_course, $parent_section, $id_resource, 'keywords', '".$pieces[$i]."')";
			$DB->execute($sql);
		}
	}*/

	//Format
	$index2 = $data->Format;
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'format'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections, id_resource, property, value) VALUES ($parent_course, $parent_section, $id_resource, 'format', '".$choices[$index2]."')";
    $DB->execute($sql);


	//LearningResourceType
	$index3 = $data->LearningResourceType;
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections, id_resource, property, value) VALUES ($parent_course, $parent_section, $id_resource, 'resourcetype', '".$choices[$index3]."')";
    $DB->execute($sql);


	//TypicalLearningTime
	$index4 = $data->TypicalLearningTime;
	$sql="SELECT property_value AS value FROM Sql973959_3.mdl_metadata_descr WHERE property_name = 'time'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO Sql973959_3.mdl_metadata(id_course, id_course_sections, id_resource, property, value) VALUES ($parent_course, $parent_section, $id_resource, 'time', '".$choices[$index4]."')";
    $DB->execute($sql);

	
    	/***********************************************/
    	/* 			FINE   		       */
    	/***********************************************/
		?>