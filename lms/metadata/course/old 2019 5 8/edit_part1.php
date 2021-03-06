<?PHP
/***********************************************/
	/* Inserimento nel DB di moodle	dei metadati   */
	/***********************************************/

	//INSERIMENTO DEI METADATI ASSOCIATI A UNA RISORSA
	
	$data = $editform->get_data();
	
	//recupero dell'id della categoria padre
	$id_category = $course->category;
	$sql = "SELECT name FROM mdl_course_categories WHERE  id = '".$id_category."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$cat = $field->name;
	}

	//recupero dell'id del corso
	$id = $course->id;
/*
	//Keywords
	$index0=$data->Keywords;
	if($index0 != NULL) {
		$pieces = explode(", ", $index0);
		for($i = 0; $i < count($pieces); $i++) {
			$sql="INSERT INTO mdl_metadata(id_course, property, value) VALUES ($id, 'keywords', '".$pieces[$i]."')";
			$DB->execute($sql);
		}
	}
*/
	//Keywords
	$lang = current_language();
    	$index0=$data->Keywords;
    	if($index0 != NULL) {
		$pieces = explode(", ", $index0);
		for($i = 0; $i < count($pieces); $i++) {
            		$temp = $pieces[$i];
             		if (($pieces2 = explode (",", $temp)) != false) {
                   		for($j = 0; $j < count($pieces2); $j++) {
                       			$lower = strtolower($pieces2[$j]);
                       			$white_space = trim($lower);
                       			if(!strlen(trim($white_space)) ==0 ) {
                       				$sql="INSERT INTO mdl_metadata(id_course, property, value, lang) VALUES ($id, 'keywords', '".$white_space."', '".$lang."')";
                       			$DB->execute($sql);
                       			}
                    		}
             		}
			else {
                    		$lower = strtolower($temp);
                    		$white_space = trim($lower);
                    		if(!strlen(trim($white_space)) ==0 ) {
                    			$sql="INSERT INTO mdl_metadata(id_course, property, value) VALUES ($id, 'keywords', '".$white_space."')";
                    		$DB->execute($sql);
                    		}
            		}
		}
	}

	//Minimal Age
	$index1 = $data->MinimalAge;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'min_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	$sql="INSERT INTO mdl_metadata(id_course, property, value) VALUES ($id, 'min_age', '".$choices[$index1]."')";
	$DB->execute($sql);

	//Maximal Age
	$index2 = $data->MaximalAge;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'max_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	$sql="INSERT INTO mdl_metadata(id_course, property, value) VALUES ($id, 'max_age', '".$choices[$index2]."')";
	$DB->execute($sql);

	//Category
	$index3 = $data->Category;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_value = '".$cat."'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	for($i = 0; $i < count($index3); $i++) {
		$value = $index3[$i];
		$sql="INSERT INTO mdl_metadata(id_course, property, value) VALUES ($id, 'category', '".$choices[$value]."')";
		$DB->execute($sql);
	}

	//Specified Required/Acquired Skills

	//preleva le skills associate alla categoria
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = '".$cat."'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	//numero di skills
	$i--;

	//Specified Required Skills
	for ($j = 0; $j <= $i; $j++) {
		//preleva l'i-esima checkbox
		$checkbox = 'checkbox1_'.$j;
		$current_checkbox = $data->$checkbox;

		if($current_checkbox == 1) {
			//preleva il grado
			$scale = 'scale1_'.$j;
			$current_scale = $data->$scale;
			$current_scale++;

			$sql="INSERT INTO mdl_metadata(id_course, property, value, grade) VALUES ($id, 's_req_skill', \"".$choices[$j]."\", '".$current_scale."')";
			$DB->execute($sql);
		}
	}

	//Specified Acquired Skills
	for ($j = 0; $j <= $i; $j++) {
		//preleva l'i-esima checkbox
		$checkbox = 'checkbox2_'.$j;
		$current_checkbox = $data->$checkbox;

		if($current_checkbox == 1) {
			//preleva il grado
			$scale = 'scale2_'.$j;
			$current_scale = $data->$scale;
			$current_scale++;

			$sql="INSERT INTO mdl_metadata(id_course, property, value, grade) VALUES ($id, 's_acq_skill', \"".$choices[$j]."\", '".$current_scale."')";
			$DB->execute($sql);
		}
	}

	/***********************************************/
	/* 		      FINE                     */
	/***********************************************/
	
?>