// genera risorse per uno specifico corso

function generate(resNumber){
	
	var resName = "autoGenRes";
	var keywords= ["autogenkey1", "autogenkey2", "autogenkey3", "autogenkey4"];
	
	for (i=0; i<resNumber; i++)
		{
			$("#id_name").val(resName+resNumber);
			$("#text-area").val(keywords[parseInt(Math.random()*4)]);
			$("#id_Format").val(parseInt(Math.random()*3));
			$("#id_LearningResourceType").val(parseInt(Math.random()*3));
			$("#id_LearningResourceType").val(parseInt(Math.random()*3));

			submitForm();
		}
	
}


function submitForm()
    {
        form=document.getElementById('mform1');
        form.target='_blank';
        form.submit();
        
    }
	
	
	
	
	
	
	////////ajax
	function send(resNumber){
 
		var resName = "autoGenRes";
		var keywords= ["autogenkey1", "autogenkey2", "autogenkey3", "autogenkey4"];

		$("#id_name").val(resName+resNumber);
		$("#text-area").val(keywords[parseInt(Math.random()*4)]);
		$("#id_Format").val(parseInt(Math.random()*3));
		$("#id_LearningResourceType").val(parseInt(Math.random()*3));
		$("#id_LearningResourceType").val(parseInt(Math.random()*3));

		$.ajax({
			url:'modedit.php',
			type:'post',
			data:$('#mform1').serialize(),
			success:function(){
				if(resNumber > 1)
				{
					send(resNumber-1);
				}
			}
		});		
		
	}
        
		
		
        



}
	
	
	
	
	
	
	