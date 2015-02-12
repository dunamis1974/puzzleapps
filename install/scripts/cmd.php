<pre>
<?

$newfld = dirname(__FILE__);

echo $cmd = "\nln -s /kunden/230052_80807/webseiten/cms/core/admin {$newfld}/admin";
echo exec($cmd);

echo $cmd = "ln -s /usr/local/lib/php_modules/5-LATEST/tidy.so {$newfld}/tidy.so";
echo exec($cmd);

?>
</pre>