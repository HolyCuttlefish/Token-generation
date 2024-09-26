<?php
	require 'vendor/autoload.php';

	use Firebase\JWT\JWT;

	$secretKey = getenv('SECRET_KEY');

	//функция для генерации токена
	function generateToken($userId)
	{
		global $secretKey;
		$tokenId = base64_encode(random_bytes(32)); //токен пользователя
		$issuedAt = time(); //время создания токена
		$notBefore = $issuedAt; //не раньше этого времени
		$expire = $issuedAt + 7200; //токен действител два часа
		$serverName = getenv('HOST');

		$token = array(
			'iat' => $issuedAt,
			'jti' => $tokenId,
			'iss' => $serverName,
			'nbf' => $notBefore,
			'exp' => $expire,
			'data' => [
				'userId' => $userId
			]
		);

		return JWT::encode($token, $secretKey, 'HS256');
	}

	//функция для проверки токена
	function validateToken($tokenValidate)
	{
		global $secretKey;
		$decoded = null;

		try
		{
			$decoded = JWT::decode($tokenValidate, $secretKey, ['HS256']);

			return $decoded['data']['userId'];
		} catch (Exception $e)
		{
			return null;
		}
	}
?>
