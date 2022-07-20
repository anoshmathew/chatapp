<?php 
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	
	require_once 'DbConnect.php';
	
	$response = array();
	
	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
			
			case 'signup':
				if(isTheseParametersAvailable(array('username','email','password','phone1','phone2'))){
					$username = $_POST['username']; 
					$email = $_POST['email']; 
					$password = md5($_POST['password']);
					$phone1 = $_POST['phone1']; 
					$phone2 = $_POST['phone2']; 
			

					$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
					$stmt->bind_param("ss", $username, $email);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'User already registered';
						$stmt->close();
					}else{
						$stmt = $conn->prepare("INSERT INTO users (username, email, password, phone1, phone2) VALUES (?, ?, ?, ?, ?)");
						$stmt->bind_param("sssss", $username, $email, $password, $phone1, $phone2);

						if($stmt->execute()){
							$stmt = $conn->prepare("SELECT id, id, username, email, phone1, phone2 FROM users WHERE username = ?"); 
							$stmt->bind_param("s",$username);
							$stmt->execute();
							$stmt->bind_result($userid, $id, $username, $email, $phone1,$phone2);
							$stmt->fetch();
							
							$user = array(
								'id'=>$id, 
								'username'=>$username, 
								'email'=>$email,
								'phone1'=>$phone1,
								'phone2'=>$phone2,
						
							);
							
							$stmt->close();
							
							$response['error'] = false; 
							$response['message'] = 'User registered successfully'; 
							$response['user'] = $user; 
						
						}
					}
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break; 
			
			case 'login':
				

				$data = json_decode(file_get_contents("php://input"), true);
				
				$username = $data['username'];	
				$password = $data['password'];	
				
				if(($username && $password) != null){
					
					//$username = $_POST['username'];
					//$password = md5($_POST['password']); 
					$password = md5($data['password']);	

					$stmt = $conn->prepare("SELECT id, username, email, phone1, phone2 FROM users WHERE username = ? AND password = ?");
					$stmt->bind_param("ss",$username, $password);
					
					$stmt->execute();
					
					$stmt->store_result();
					if($stmt->num_rows > 0){
						$token = uniqid();
						$stmt->bind_result($id, $username, $email, $phone1,$phone2);
						$stmt->fetch();
						
						$stmt = $conn->prepare("UPDATE users SET token = ? WHERE username = ? AND password = ?");
						$stmt->bind_param("sss", $token,$username,$password);
						$stmt->execute();
						$stmt->store_result();
						
						
						$user = array(
							'id'=>$id, 
							'username'=>$username, 
							'token'=>$token,
							'email'=>$email,
							'phone1'=>$phone1,
							'phone2'=>$phone2,
							);
						
						$response['error'] = false; 
						$response['message'] = 'Login successfull'; 
						$response['user'] = $user; 
					
				}
					else{
						$response['error'] = true; 
						$response['message'] = 'Invalid username or password';
					}
			
				}
				
				
			break;
			case 'getmessage':
				$user = array();
				$sql = "SELECT id, username, email, phone1, phone2 FROM users";
				$result = $conn->query($sql);				
				if ($result->num_rows > 0) {
				    // output data of each row
				    while($row = $result->fetch_assoc()) {
				        $e = array(
							'id'=>$row["id"], 
							'username'=>$row["username"], 
							'email'=>$row["email"],
							'phone1'=>$row["phone1"],
							'phone2'=>$row["phone2"],
						);
						array_push($user, $e);
				    }
						$response['error'] = false; 
						$response['message'] = 'Got Data'; 
					} 
					else 
					{
					    $response['error'] = true; 
						$response['message'] = 'No Data'; 
					}
					$conn->close();
	
					$response['user'] = $user; 
	
				
			break;

			case 'getmsg':
				

				$data = json_decode(file_get_contents("php://input"), true);
				
				$username = 'abc';	
				$password = 'aacd';	
				
				if(($username && $password) != null){
					
					//$username = $_POST['username'];
					//$password = md5($_POST['password']); 
					//$password = md5($data['password']);	

					$user = array();
					$sql = "SELECT id, sender, receiver, message, data FROM messages";
					$result = $conn->query($sql);				
					if ($result->num_rows > 0) 
					{
					    while($row = $result->fetch_assoc()) 
						{
					        $e = array(
								'id'=>$row["id"], 
								'msg_id'=>$row["msg_id"],
								'sender'=>$row["sender"], 
								'receiver'=>$row["receiver"],
								'message'=>$row["message"],
								'data'=>$row["data"],
							);
							array_push($user, $e);
					    }
							$response['error'] = false; 
							$response['message'] = 'Got Data'; 
					} 
					else 
					{
					    $response['error'] = true; 
						$response['message'] = 'No Data'; 
					}
					$conn->close();	
					$response['user'] = $user; 
			
				}
			break;
			case 'changepassword': 
				$data = json_decode(file_get_contents("php://input"), true);
				$username = $data['username'];	
				$oldpassword = md5($data['oldpassword']);
				$newpassword = md5($data['newpassword']);
				if(($username && $oldpassword && $newpassword) != null){
					
					//$username = $_POST['username'];
					//$password = md5($_POST['password']); 
		

					$stmt = $conn->prepare("SELECT id, username, email, phone1, phone2 FROM users WHERE username = ? AND password = ?");
					$stmt->bind_param("ss",$username, $oldpassword);
					
					$stmt->execute();
					
					$stmt->store_result();
					if($stmt->num_rows > 0){
						
						
						
						$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ? AND password = ?");
						$stmt->bind_param("sss",  $newpassword, $username,$oldpassword);
						$stmt->execute();
						$stmt->store_result();

						//$stmt->bind_result($id, $username, $email, $phone1,$phone2);
						//$stmt->fetch();
						
						//$user = array(
						//	'id'=>$id, 
						//	'username'=>$username, 							
						//	'email'=>$email,
						//	'phone1'=>$phone1,
						//	'phone2'=>$phone2,	
						//);
						
						$response['error'] = false; 
						$response['message'] = 'Password Changed'; 
						//$response['user'] = $user; 
					
				}
					else{
						$response['error'] = true; 
						$response['message'] = 'Invalid username or password';
					}
			
				}
				else{
					$response['error'] = true; 
					$response['message'] = 'Input Missing';
				}
			break;
			case 'edituser': 
				$data = json_decode(file_get_contents("php://input"), true);
				$username = $data['username'];	
				$newusername = $data['newusername'];
				$email = $data['email'];
				$phone1 = $data['phone1'];
				$phone2 = $data['phone2'];
				$password = $data['password'];
				
				if(($username && $password && $newusername && $email && $phone1 && $phone2) != null){
					
					//$username = $_POST['username'];
					//$password = md5($_POST['password']); 
					$password = md5($data['password']);	

					$stmt = $conn->prepare("SELECT id, username, email, phone1, phone2 FROM users WHERE username = ? AND password = ?");
					$stmt->bind_param("ss",$username, $password);
					
					$stmt->execute();
					
					$stmt->store_result();
					if($stmt->num_rows > 0){
						
						
						
						$stmt = $conn->prepare("UPDATE users SET username = ?, email=?, phone1=?, phone2=? WHERE username = ? AND password = ?");
						$stmt->bind_param("ssssss", $newusername,$email,$phone1,$phone2, $username, $password);
						$stmt->execute();
						$stmt->store_result();
						//$stmt->bind_result($id, $username, $email, $phone1,$phone2);
						//$stmt->fetch();
						
						//$user = array(
						//	'id'=>$id, 
						//	'username'=>$username, 
						//	'email'=>$email,
						//	'phone1'=>$phone1,
						//	'phone2'=>$phone2,	
						//);
						
						$response['error'] = false; 
						$response['message'] = 'User Data Updated'; 
						//$response['user'] = $user; 
					
				}
					else{
						$response['error'] = true; 
						$response['message'] = 'Invalid username or password';
					}
			
				}
				else{
					$response['error'] = true; 
					$response['message'] = 'Input Missing';
				}
			break;
			
			case 'get_link':
				$data = json_decode(file_get_contents("php://input"), true);
				
				$username = $data['username'];	
				$password = $data['password'];	
				
				if(($username && $password) != null){
					
					//$username = $_POST['username'];
					//$password = md5($_POST['password']); 
					$password = md5($data['password']);	

					$stmt = $conn->prepare("SELECT id, username, email, phone1, phone2 FROM users WHERE username = ? AND password = ?");
					$stmt->bind_param("ss",$username, $password);
					
					$stmt->execute();
					
					$stmt->store_result();
			
					
					
					if($stmt->num_rows > 0){
						$share_id = uniqid();
						$stmt->bind_result($id, $username, $email, $phone1,$phone2);
						$stmt->fetch();
						
						$stmt = $conn->prepare("UPDATE users SET share_id = ? WHERE username = ? AND password = ?");
						$stmt->bind_param("sss", $share_id,$username,$password);
						$stmt->execute();
						$stmt->store_result();
						
						
						$user = array(
							'id'=>$id, 
							'username'=>$username, 
							'share_id'=>$share_id
							
						);
						
						$response['error'] = false; 
						$response['message'] = 'Share ID Generated!'; 
						$response['user'] = $user; 
					
				}
					else{
						$response['error'] = true; 
						$response['message'] = 'Invalid username or password';
					}
			
				}
			break;
			
			default: 
				$response['error'] = true; 
				$response['message'] = 'Invalid Operation Called';
		}
		
	}
	else if(isset($_GET['check_link'])){
		$share_id =$_GET['check_link'];
		
		if(($share_id) != null){
			$stmt = $conn->prepare("SELECT username FROM users WHERE share_id = ?");
			$stmt->bind_param("s",$share_id);			
			$stmt->execute();		
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$stmt->bind_result($username);
				$stmt->fetch();
				$user = array(					
					'username'=>$username, 										
				);
				$response['error'] = false; 					
				$response['user'] = $user;
				$response['message'] = 'Logged in';  
			}
			else{
				$response['error'] = true; 
				$response['message'] = 'Wrong URL or Link Expired';
			}
			//$response['share_id'] = $share_id;
		}
		else{
			$response['error'] = true; 
			$response['message'] = 'No ID Dectected!';
		}
	}	
	else{
		$response['error'] = true; 
		$response['message'] = 'Invalid API Call';
	}
	
	echo json_encode($response);
	
	function isTheseParametersAvailable($params){
		
		foreach($params as $param){
			if(!isset($_POST[$param])){
				return false; 
			}
		}
		return true; 
	}




	