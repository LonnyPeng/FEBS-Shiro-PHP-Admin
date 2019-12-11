<?php
/**
 *                    _ooOoo_
 *                   o8888888o
 *                   88" . "88
 *                   (| -_- |)
 *                   O\  =  /O
 *                ____/`---'\____
 *              .'  \\|     |//  `.
 *             /  \\|||  :  |||//  \
 *            /  _||||| -:- |||||-  \
 *            |   | \\\  -  /// |   |
 *            | \_|  ''\---/''  |   |
 *            \  .-\__  `-`  ___/-. /
 *          ___`. .'  /--.--\  `. . __
 *       ."" '<  `.___\_<|>_/___.'  >'"".
 *      | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *      \  \ `-.   \_ __\ /__ _/   .-` /  /
 * ======`-.____`-.___\_____/___.-`____.-'======
 *                    `=---='
 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *          佛祖保佑       永无BUG
 */

namespace app\lonny\controller\Plugin;

class Latlng
{
	public static function address($latlng = array())
	{
		if (!$latlng || !is_array($latlng)) {
			return false;
		}

		$addressArr = array();

		$urlInfos = $regeoArr = array();
		foreach ($latlng as $key => $value) {
			if (!$value) {
				continue;
			}

			$regeoArr[$key] = $value;
			if (count($regeoArr) == 20) {
				$urlInfos[implode(",", array_keys($regeoArr))] = array(
				    'url' => "http://restapi.amap.com/v3/geocode/regeo",
				    'params' => array(
				        'key' => GAO_DE_KEY, 
				        'batch' => "true",
				        'location' => implode("|", $regeoArr),
				    ),
				);

				$regeoArr = array();
			}
		}

		if ($regeoArr) {
			$urlInfos[implode(",", array_keys($regeoArr))] = array(
			    'url' => "http://restapi.amap.com/v3/geocode/regeo",
			    'params' => array(
			        'key' => GAO_DE_KEY, 
			        'batch' => "true",
			        'location' => implode("|", $regeoArr),
			    ),
			);
		}

		if (!$urlInfos) {
			return false;
		}
		
		if (count($urlInfos) == 1) {
			$result = self::curl(reset($urlInfos));
			$result = json_decode($result, true);
			if ($result['infocode'] != '10000') {
				return false;
			}

			$url_key = array_keys($urlInfos);

			$map = explode(",", reset($url_key));
			$regeocodes = $result['regeocodes'];

			foreach ($map as $key => $value) {
				$addressArr[$value] = isset($regeocodes[$key]['formatted_address']) && $regeocodes[$key]['formatted_address'] ? $regeocodes[$key]['formatted_address'] : '';
			}
		} else {
			$urlInfosArr = array();
			$n = 0;
			foreach ($urlInfos as $key => $row) {
				if (isset($urlInfosArr[$n])) {
					$urlInfosArr[$n][$key] = $row;
				} else {
					$urlInfosArr[$n] = array($key => $row);
				}

				if (count($urlInfosArr[$n]) == 50) {
					$n++;
				}
			}

			foreach ($urlInfosArr as $row) {
				$results = self::curlMulti($row);
				foreach ($results as $key => $result) {
					$result = json_decode($result, true);
					if ($result['infocode'] != '10000') {
						continue;
					}

					$map = explode(",", $key);
					$regeocodes = $result['regeocodes'];
					foreach ($map as $ke => $value) {
						$addressArr[$value] = isset($regeocodes[$ke]['formatted_address']) && $regeocodes[$ke]['formatted_address'] ? $regeocodes[$ke]['formatted_address'] : '';
					}
				}
			}
		}

		return $addressArr;
	}

	/**
	 * Use the curl virtual browser
	 *
	 * @param array $urlInfo = array('url' => "https://www.baidu.com/", 'params' => array('key' => 'test'), 'cookie' => 'cookie')
	 * @param string $type = 'GET|POST'
	 * @param boolean $info = false|true
	 * @return string|array
	 */
	public static function curl($urlInfo, $type = "GET", $info = false) {
	    $type = strtoupper(trim($type));

	    if (isset($urlInfo['cookie'])) {
	        $cookie = $urlInfo['cookie'];
	        unset($urlInfo['cookie']);
	    }

	    if ($type == "POST") {
	        $url = $urlInfo['url'];
	        $data = $urlInfo['params'];
	    } else {
	        $urlArr = parse_url($urlInfo['url']);

	        if (isset($urlInfo['params'])) {
	            $params = "";
	            foreach ($urlInfo['params'] as $key => $row) {
	                if (is_array($row)) {
	                    foreach ($row as $value) {
	                        if ($params) {
	                            $params .= "&" . $key . "=" . $value;
	                        } else {
	                            $params .= $key . "=" . $value;
	                        }
	                    }
	                } else {
	                    if ($params) {
	                        $params .= "&" . $key . "=" . $row;
	                    } else {
	                        $params .= $key . "=" . $row;
	                    }
	                }
	            }
	            
	            if (isset($urlArr['query'])) {
	                if (preg_match("/&$/", $urlArr['query'])) {
	                    $urlArr['query'] .= $params;
	                } else {
	                    $urlArr['query'] .= "&" . $params;
	                }
	            } else {
	                $urlArr['query'] = $params;
	            }
	        }

	        if (isset($urlArr['host'])) {
	            if (isset($urlArr['scheme'])) {
	                $url = $urlArr['scheme'] . "://" . $urlArr['host'];
	            } else {
	                $url = $urlArr['host'];
	            }

	            if (isset($urlArr['port'])) {
	                $url .= ":" . $urlArr['port'];
	            }
	            if (isset($urlArr['path'])) {
	                $url .= $urlArr['path'];
	            }
	            if (isset($urlArr['query'])) {
	                $url .= "?" . $urlArr['query'];
	            }
	            if (isset($urlArr['fragment'])) {
	                $url .= "#" . $urlArr['fragment'];
	            }
	        } else {
	            $url = $urlInfo['url'];
	        }
	    }
	    
	    $httpHead = array(
	        "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
	        "Cache-Control:no-cache",
	        "Connection:keep-alive",
	        "Pragma:no-cache",
	        "Upgrade-Insecure-Requests:1",
	    );
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    if (isset($cookie)) {
	        curl_setopt($ch, CURLOPT_COOKIE , $cookie);
	    }
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHead);
	    curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	    if ($type == "POST") {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    } else {
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	    }
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36");
	    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_NOBODY, 0);
	    $result = curl_exec($ch);
	    $curlInfo = curl_getinfo($ch);
	    curl_close($ch); 
	    
	    if ($info) {
	        return $curlInfo;
	    } else {
	        return $result;
	    }
	}

	/**
	 * Use the curl multi virtual browser
	 *
	 * @param array $urlInfos = array(
	 *     array('url' => "https://www.baidu.com/", 'params' => array('key' => 'test'), 'cookie' => 'cookie', 'type' => 'GET'),
	 *     array('url' => "https://www.google.com/", 'params' => array('key' => 'test'), 'cookie' => 'cookie', 'type' => 'POST'),
	 * )
	 * @param string $type = 'GET|POST'
	 * @param boolean $info = false|true
	 * @return string|array
	 */
	public static function curlMulti($urlInfos = array()) {
	    $curlArray = $data =  array();
	    $httpHead = array(
	        "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
	        "Cache-Control:no-cache",
	        "Connection:keep-alive",
	        "Pragma:no-cache",
	        "Upgrade-Insecure-Requests:1",
	    );
	    $mh = curl_multi_init();

	    foreach($urlInfos as $key => $urlInfo) {
	        if (isset($urlInfo['type'])) {
	            $type = strtoupper(trim($urlInfo['type']));
	            unset($urlInfo['type']);
	        } else {
	            $type = 'GET';
	        }

	        if (isset($urlInfo['cookie'])) {
	            $cookie = $urlInfo['cookie'];
	            unset($urlInfo['cookie']);
	        }

	        if ($type == "POST") {
	            $url = $urlInfo['url'];
	            $data = $urlInfo['params'];
	        } else {
	            $urlArr = parse_url($urlInfo['url']);

	            if (isset($urlInfo['params'])) {
	                $params = "";
	                foreach ($urlInfo['params'] as $ke => $row) {
	                    if (is_array($row)) {
	                        foreach ($row as $value) {
	                            if ($params) {
	                                $params .= "&" . $ke . "=" . $value;
	                            } else {
	                                $params .= $ke . "=" . $value;
	                            }
	                        }
	                    } else {
	                        if ($params) {
	                            $params .= "&" . $ke . "=" . $row;
	                        } else {
	                            $params .= $ke . "=" . $row;
	                        }
	                    }
	                }

	                if (isset($urlArr['query'])) {
	                    if (preg_match("/&$/", $urlArr['query'])) {
	                        $urlArr['query'] .= $params;
	                    } else {
	                        $urlArr['query'] .= "&" . $params;
	                    }
	                } else {
	                    $urlArr['query'] = $params;
	                }
	            }

	            if (isset($urlArr['host'])) {
	                if (isset($urlArr['scheme'])) {
	                    $url = $urlArr['scheme'] . "://" . $urlArr['host'];
	                } else {
	                    $url = $urlArr['host'];
	                }

	                if (isset($urlArr['port'])) {
	                    $url .= ":" . $urlArr['port'];
	                }
	                if (isset($urlArr['path'])) {
	                    $url .= $urlArr['path'];
	                }
	                if (isset($urlArr['query'])) {
	                    $url .= "?" . $urlArr['query'];
	                }
	                if (isset($urlArr['fragment'])) {
	                    $url .= "#" . $urlArr['fragment'];
	                }
	            } else {
	                $url = $urlInfo['url'];
	            }
	        }

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        if (isset($cookie)) {
	            curl_setopt($ch, CURLOPT_COOKIE , $cookie);
	        }
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHead);
	        curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	        if ($type == "POST") {
	            curl_setopt($ch, CURLOPT_POST, 1);
	            @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        } else {
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	        }
	        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36");
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_NOBODY, 0);

	        $curlArray[$key] = $ch;
	        curl_multi_add_handle($mh, $curlArray[$key]);
	    }

	    $running = 0;
	    do {
	        usleep(10000);
	        curl_multi_exec($mh, $running);
	    } while($running > 0);

	    foreach($urlInfos as $key => $urlInfo) {
	        $data[$key] = curl_multi_getcontent($curlArray[$key]);
	        curl_multi_remove_handle($mh, $curlArray[$key]);
	    }
	    curl_multi_close($mh);

	    return $data;
	}
}