<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>S3 tutorial</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>

<body>-->
    	<?php
    	$dir    = '/home/john/projects/Amazon/upload';
		$files = array_values(array_diff(scandir($dir), array('..', '.')));

    	error_reporting(0);
			//include the S3 class
			if (!class_exists('S3'))require_once('S3.php');
			
			//AWS access info
			if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIYLZFJPV2D6PAM4A');
			if (!defined('awsSecretKey')) define('awsSecretKey', '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');
			
			$bucketName = 'solfusetestbucket'; // Temporary bucket
			$path = 'test';
			$lifetime = 3600; // Period for which the parameters are valid
			$maxFileSize = (1024 * 1024 * 50); // 50 MB

			$metaHeaders = array('uid' => 123);
			$requestHeaders = array(
			    'Content-Type' => 'application/octet-stream',
			    'Content-Disposition' => 'attachment; filename=${filename}'
			);

			//instantiate the class
			$s3 = new S3(awsAccessKey, awsSecretKey);

			//create a new bucket
			$s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);
		
			/*$f_count = count($files);
			for($i=0; $i<$f_count; $i++){
				//move the file
				/*if ($s3->putObjectFile($dir.'/'.$files[$i], $bucketName, $path.'/'.$files[$i], S3::ACL_PUBLIC_READ)) {
					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}

				if(is_file($dir.'/'.$files[$i])){
					echo "hi";
    				unlink(realpath($dir) . '/' . $files[$i]); // delete file
				}else{
					echo 'test';
				}
			}*/
			foreach($files as $file){
				//move the file
				if ($s3->putObjectFile($dir.'/'.$file, $bucketName, $path.'/'.$file, S3::ACL_PUBLIC_READ)) {

					if(is_file($dir.'/'.$file)){
	    				unlink($dir . '/' . $file); // delete file
					}

					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
			}
			
			//check whether a form was submitted
			/*if(isset($_POST['Submit'])){
			
				$f_count = count($_FILES['upload']['name']);

				for($i=0; $i<$f_count; $i++){
					//retreive post variables
					$fileName = $_FILES['upload']['name'][$i];
					$fileTempName = $_FILES['upload']['tmp_name'][$i];
				
					//create a new bucket
					$s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);
				
					//move the file
					if ($s3->putObjectFile($fileTempName, $bucketName, $path.'/'.$fileName, S3::ACL_PUBLIC_READ)) {
						echo "<strong>We successfully uploaded your file.</strong>";
					}else{
						echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
					}
				}
			}*/
		?>
<!--<h1>Upload a file</h1>
<p>Please select a file by clicking the 'Browse' button and press 'Upload' to start uploading your file.</p>
   	<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input name="upload[]" multiple="multiple" type="file" />
      <input name="Submit" type="submit" value="Upload">
	</form>
<h1>All uploaded files</h1>
<?php
	// Get the contents of our bucket
	$contents = $s3->getBucket($bucketName);
	foreach ($contents as $file){
	
		$fname = $file['name'];
		$furl = "http://".$bucketName.".s3.amazonaws.com/".$fname;
		
		//output a link to the file
		echo "<a href=\"$furl\">$fname</a><br />";
	}
?>
</body>
</html>-->