<?php

class Docebo_Docebo_Helper_DoceboRequest extends Mage_Core_Helper_Abstract {

	
	public function request($service, $params, $user=false, $api=false) {
		$docebo_url = Mage::getStoreConfig('web/backend/docebo_url');
		$docebo_use_ssl = false; //variable_get('docebo_use_ssl');
		$api = (!empty($api) ? $api : 'user');

		ini_set('arg_separator.output', '&');

		if (!empty($docebo_url)) {
			$auth = self::doceboGetAuth($user);

			if ($auth) {
				$url =$docebo_url . "api/rest.php?q=/restAPI/" . $api . "/" . $service . "/&auth=" . $auth;				

				if ($docebo_use_ssl) {
					$opt =array();
					$opt[CURLOPT_SSL_VERIFYPEER] = FALSE;
					$opt[CURLOPT_SSL_VERIFYHOST] = 2;
					$http_req->setOptions($opt);
				}

				$http_req =Mage_HTTP_Client::getInstance();
				$http_req->post($url, $params);

				$result = $http_req->getBody();
			}
			else {
				$result = $auth;
			}
		}

  return $result;
	}

	
	function authRequest($third_party = false) {

		$docebo_url = Mage::getStoreConfig('web/backend/docebo_url');
		$docebo_user = Mage::getStoreConfig('web/backend/docebo_user');
		$docebo_pass = Mage::getStoreConfig('web/backend/docebo_pass');
		$docebo_use_ssl = false; //variable_get('docebo_use_ssl');

		ini_set('arg_separator.output', '&');
		
		$tp_query = (!empty($third_party) ? '&third_party='.$third_party : '');

		$url = $docebo_url."api/rest.php?q=/restAPI/auth/authenticate&username=".$docebo_user."&password=".$docebo_pass.$tp_query;

		$http_req =Mage_HTTP_Client::getInstance();
		$http_req->get($url);
		
		$result = $http_req->getBody();

		$xml = ($result ? simplexml_load_string($result) : false);

		if ($xml && $xml->success == 'true') {
			$res = (string) $xml->token;
			$_SESSION['docebo_auth_time'] = time();
			$_SESSION['docebo_auth_user'] = $third_party;
			$_SESSION['docebo_auth_token'] = $res;
			return $res;
		}
		else {
			return false;
		}
	}


	public function doceboGetAuth($user=false) {

		$user_changed = (!empty($_SESSION['docebo_auth_user']) && $_SESSION['docebo_auth_user'] == $user ? false : true);

		$auth = true;
		if ($user_changed || !isset($_SESSION['docebo_auth_time']) || time() - $_SESSION['docebo_auth_time'] > 3600) {
			$auth = self::authRequest($user);
		}
		else if (isset($_SESSION['docebo_auth_token'])) {
			$auth = $_SESSION['docebo_auth_token'];
		}

		return $auth;
	}

}