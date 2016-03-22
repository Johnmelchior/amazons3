<?php
	//Directory to scan files in local folder hardcoded
	$dir    = '/home/john/public_html/upload';
	$files = array_values(array_diff(scandir($dir), array('..', '.')));

	if(!empty($files)){
		error_reporting(0);
		//include the S3 class
		if (!class_exists('S3'))require_once('S3.php');
		
		//AWS access info
		if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIYLZFJPV2D6PAM4A');
		if (!defined('awsSecretKey')) define('awsSecretKey', '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');
		
		$bucketName = 'solfusetestbucket'; // Temporary bucket
		$path = '';
		$lifetime = 3600; // Period for which the parameters are valid
		$maxFileSize = (1024 * 1024 * 50); // 50 MB

		$metaHeaders = array('uid' => 123);
		$requestHeaders = array(
		    'Content-Type' => 'application/octet-stream',
		    'Content-Disposition' => 'attachment; filename=${filename}'
		);

		//instantiate the class
		$s3 = new S3(awsAccessKey, awsSecretKey);

		//Get list of buckets
		$Allbuckets = $s3->listBuckets($bucketName);

		//Get Access Control List for the bucket
		$bucketacl = $s3->getAccessControlPolicy($bucketName);

		foreach($Allbuckets['buckets'] as $bucket){

			//Check the the bucket is exist and have permission
			if((isset($bucket['name']) && $bucket['name'] == $bucketName) && (isset($bucketacl)){
				//create a new bucket
				//$s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);
	
		foreach($files as $file){
			//move the file to s3 bucket
			if ($s3->putObjectFile($dir.'/'.$file, $bucketName, $file, S3::ACL_PUBLIC_READ)) {

				if(is_file($dir.'/'.$file)){
    				unlink($dir . '/' . $file); // delete file
				}
				
				//Append rewrite rule in .htaccess file here				
				if($file != '.htaccess'){
					$furl = "http://".$bucketName.".s3.amazonaws.com/".$file;
					echo $furl."<br>";
					$contents = $s3->getBucket($bucketName);
					foreach ($contents as $fileIn){
						$fname = $fileIn['name'];
						if($fname == '.htaccess'){
							//echo $furl."<br>";
							//Directory hardcoded for testing
							$saveTo = '/home/john/public_html/projects/amazon/aws/john/.htaccess';
							chmod($saveTo, 0755);
							$test = $s3->getObject($bucketName, $fname, $saveTo);
							$rewriteRule = "\n\nRewriteRule ^".$file." https://s3.amazonaws.com/solfusetestbucket/".$file." [L]";
							// $rewriteRule = "\n\nRewriteRule ^test/(*) https://s3.amazonaws.com/solfusetestbucket/test.php/$1 [L]";
							file_put_contents($saveTo, $rewriteRule, FILE_APPEND | LOCK_EX);
							//echo "<pre>";print_r($test);exit;
							//output a link to the file
							//echo "<a href=\"$furl\">$fname</a><br />";
						}	
						$s3->putObjectFile($saveTo, $bucketName, $fname, S3::ACL_PUBLIC_READ);
					}
				}


				/*$to      = 'john@solutionfuse.com';
				$subject = 'New File uploaded to bucket';
				$message = "The new file ". $file ." has been detected and uploaded to the bucket " . $bucketName;
				$headers = 'From: john@g2tsolutions.com' . "\r\n" .
				    'Reply-To: john@g2tsolutions.com' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();

				mail($to, $subject, $message, $headers);*/
			}else{
				/*$to      = 'john@solutionfuse.com';
				$subject = 'Error uploading to bucket';
				$message = "Error uploading;
				$headers = 'From: john@g2tsolutions.com' . "\r\n" .
				    'Reply-To: john@g2tsolutions.com' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();

				mail($to, $subject, $message, $headers);*/
			}
		}
			}else{
				/*$to      = 'john@solutionfuse.com';
				$subject = 'Bucket doesnot exist';
				$message = "The bucket". $bucketName ." doesnot exist";
				$headers = 'From: john@g2tsolutions.com' . "\r\n" .
				    'Reply-To: john@g2tsolutions.com' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();

				mail($to, $subject, $message, $headers);*/
			}
		}
	}
    		
?>