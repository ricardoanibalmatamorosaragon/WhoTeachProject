<?php

/***********************************************/
	/* Inserimento nel DB di moodle	dei metadati   */
	/***********************************************/

	//INSERIMENTO DEI METADATI ASSOCIATI A UNA RISORSA

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
	$sql="SELECT max(id) AS id_r FROM mdl_course_modules";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$id_resource = $field->id_r;
	}
	
	//recupero valutazione del corso
	$sql = "SELECT ROUND(AVG(mdl_block_rate_course.rating*2)) as grade
			FROM mdl_block_rate_course
			WHERE mdl_block_rate_course.course = ".$parent_course."
			";
	if($DB->get_record_sql($sql)->grade != null)
		$courseGrade = $DB->get_record_sql($sql)->grade;
	else 
		$courseGrade = 0;
	//voto corso recuperato..

	//Language
	$index0 = $data->Language;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'language'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'language', '".$choices[$index0]."', $courseGrade)";
    $DB->execute($sql);


	//Keywords
	$lang = current_language();
	$index1=$data->Keywords;
	if($index1 != NULL) {
		$pieces = explode(", ", $index1);
		for($i = 0; $i < count($pieces); $i++) {
            		$temp = $pieces[$i];
             		if(($pieces2 = explode (",", $temp)) != false) {
                   		for($j = 0; $j < count($pieces2); $j++) {
                       			$lower = strtolower($pieces2[$j]);
                       			$white_space = trim($lower);
                       			if(!strlen(trim($white_space)) ==0 ) {
                       			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections,id_resource, property, value, courseGrade, lang) VALUES ($parent_course, $parent_section,$id_resource, 'keywords', \"".$white_space."\", $courseGrade, '".$lang."')";
                       			$DB->execute($sql);
                       			}
                    		}
             		}
			else { 
                    		$lower = strtolower($temp);
                    		$white_space = trim($lower);
                    		if(!strlen(trim($white_space)) ==0 ) {
                    			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections,id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section,$id_resource, 'keywords', \"".$white_space."\", $courseGrade)";
                    		$DB->execute($sql);
                    		}
            		}
		}
	}

	//Format
	$index2 = $data->Format;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'format'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'format', '".$choices[$index2]."', $courseGrade)";
    $DB->execute($sql);


	//LearningResourceType
	$index3 = $data->LearningResourceType;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'resourcetype', '".$choices[$index3]."', $courseGrade)";
    $DB->execute($sql);


	//TypicalLearningTime
	$index4 = $data->TypicalLearningTime;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'time'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'time', '".$choices[$index4]."', $courseGrade)";
    $DB->execute($sql);

	
    	/***********************************************/
    	/* 			FINE   		       */
    	/***********************************************/
?>