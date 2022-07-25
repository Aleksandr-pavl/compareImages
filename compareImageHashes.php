</php
/*
 * Сравнить два хэша изображений
 * @param string $hash1 хэш первого изображения в формате base64
 * @param string $hash2 хэш второго изображения в таком же формате
 * @param float $epsilon Максимальная относительная ошибка. 1 это 100%, 0.5 это 50% и так далее.
 * @param boolean $error Ссылка на переменную, в которую будет записана величина ошибки (число от 0 до 1)
 * @param boolean $alreadyDecoded Флаг, указывающий, что переданные хэши уже декодированы из base64
 * @return boolean возвращает true/false в зависимости от того, соответствуют ли друг другу хэши
*/
function compareImageHashes($hash1, $hash2, $epsilon = 0.01, &$error = 0, $alreadyDecoded = false)
{
    $error = 1;
    if ($epsilon == 0)
    {
        return $hash1 == $hash2;
    }
    else
    {
        if ($hash1 == $hash2) return true;
	if (!$alreadyDecoded)
	{
            $h1 = base64_decode($hash1);
            $h2 = base64_decode($hash2);
        }
	else
	{
            $h1 = $hash1;
            $h2 = $hash2;
	}
 
        if (strlen($h1) != strlen($h2)) return false;
 
	$l = strlen($h1);
	$error = 0;
	$bytes1 = unpack("C*", $h1);
	$bytes2 = unpack("C*", $h2);
 
	for ($i=0;$i<$l;$i++)
	{
            $b1 = $bytes1[$i+1];
            $b2 = $bytes2[$i+1];
            if ($b1 != $b2)
            {
                $delta = abs($b1 - $b2);
                $mid = ($b1 + $b2) / 2;
                if ($delta > 0)
                {
                    $e = $delta / $mid;
                    $error += $e / $l;
                    if ($error > $epsilon) return false;
                }
            }
        }
        return $error <= $epsilon;
    }
}