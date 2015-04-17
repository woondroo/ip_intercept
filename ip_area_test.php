<?php
/** 引入IP段 */
$aryIps = require( 'result.php' );

/**
 * 获得用户的真实IP地址
 *
 * @return  string
 */
function realIp()
{
	if (isset($_SERVER))
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

			/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
			foreach ($arr AS $ip)
			{
				$ip = trim($ip);

				if ($ip != 'unknown')
				{
					$realip = $ip;

					break;
				}
			}
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			if (isset($_SERVER['REMOTE_ADDR']))
			{
				$realip = $_SERVER['REMOTE_ADDR'];
			}
			else
			{
				$realip = '0.0.0.0';
			}
		}
	}
	else
	{
		if (getenv('HTTP_X_FORWARDED_FOR'))
		{
			$realip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_CLIENT_IP'))
		{
			$realip = getenv('HTTP_CLIENT_IP');
		}
		else
		{
			$realip = getenv('REMOTE_ADDR');
		}
	}

	preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
	$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

	return $realip;
}

/**
 * 是否需要拒绝
 *
 * @param array $_aryIps 拒绝的IP列表
 * @param string $_strIp IP地址
 *
 * @return boolean
 */
function isNeedReject( $_aryIps = array() , $_strIp )
{
	// 判断是否是合法IP
	preg_match( '/\d+\.\d+\.\d+\.\d+/' , $_strIp , $res );
	if ( empty( $res[0] ) )
		return true;

	// 转换IP
	$tmp = explode( '.' , $_strIp );
	$tmps = '';
	foreach ( $tmp as $t )
		$tmps .= sprintf("%03d", $t);

	// 转换后的IP值
	$intIp = intval( $tmps );
	foreach ( $_aryIps as $ips )
	{
		// 如果在某一个IP范围内，则不允许通过
		if ( $intIp >= $ips[0] && $intIp <= $ips[1] )
			return true;
	}

	return false;
}

/** 获得IP */
$ip = realIp();
echo "Your ip address is: {$ip}\n";

/** 判断是否在拒绝的范围内 */
if ( isNeedReject( $aryIps , $ip ) === true )
	echo "Your ip is in the china, reject!\n";
else
	echo "Congritulations! You have a good ip!\n";
?>
