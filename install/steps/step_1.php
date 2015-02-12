<table align="center" width="500" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="title" colspan="2">Check for Requirements</td>
</tr>
<tr>
    <td class="item">PHP Version >= 5.0</td>
    <td align="left"><? echo phpversion() < '5.0' ? '<b class="error">'.$failedImg.' ('.phpversion().'): Puzzle Apps will not work. Please upgrade!</b>' : '<b class="ok">'.$okImg.'</b><span class="item"> ('.phpversion().')</span>';?></td>
</tr>
<tr>
    <td class="item"><li>Server API</li></td>
    <td align="left"><? echo (php_sapi_name() != "cgi") ? '<b class="ok">'.$okImg.'</b><span class="item"> ('.php_sapi_name().')</span>' : '<b class="error">'.$failedImg.' CGI mode is likely to have problems</b>';?></td>
</tr>
<tr>
    <td class="item"><li>Is PEAR::DB present (optional)</li></td>
    <td align="left"><? echo DetectPEARDB() ? '<b class="ok">'.$okImg.'</b>' : '<b class="error">'.$failedImg.'</b><span class="error"> PEAR::DB was not found in your include path!</span>';?></td>
</tr>
<tr>
    <td class="item"><li>XSL Support (PHP5)</li></td>
    <td align="left"><? echo extension_loaded('xsl') ? '<b class="ok">'.$okImg.'</b>' : '<b class="error">'.$failedImg.'</b>';?></td>
</tr>
<tr>
    <td class="item"><li>Zlib compression Support</li></td>
    <td align="left"><? echo extension_loaded('zlib') ? '<b class="ok">'.$okImg.'</b>' : '<b class="error">'.$failedImg.'</b>You will not be able to download Templates/Images/etc.';?></td>
</tr>
<tr>
    <td class="item"><li>File Uploads</li></td>
    <td align="left"><? echo get_cfg_var('file_uploads') ? '<b class="ok">'.$okImg.'</b><span class="item"> (Max File Upload Size: '. ini_get('upload_max_filesize') .')</span>' : '<b class="error">'.$failedImg.'</b><span class="error"> Upload functionality will not be available</span>';?></td>
</tr>

<tr>
    <td class="title" colspan="2"><br />Database Connectors</td>
</tr>
<tr>
    <td class="item" colspan="2">
        The next tests check for database support compiled with php. We use the PEAR::DB database abstraction layer which comes with drivers for
        many databases. Consult the PEAR::DB documentation for details. <br />
        For most users: MySQL will probably be the database of your choice - make sure MySQL Support is available.
        <br /><br />
        Currently "Puzzle Apps CMS" supports only <strong>MySql</strong>, <strong>SQLite</strong> and <strong>PostgreSql</strong>.<br />
        If you need support for other database plase write to <a href="mailto:puzzle@planicus.com">Puzzle Apps Team</a><br /><br />
    </td>
</tr>
<tr>
    <td class="item" colspan="2"><strong>Supported</strong></td>
</tr>
<tr>
    <td class="item"><li>MySQL Support</li></td>
    <td align="left"><? echo function_exists( 'mysql_connect' ) ? '<b class="ok">'.$okImg.'</b><span class="item"> ('.mysql_get_client_info().')</span>' : '<span class="error">'.$failedImg.' Not available</span>';?></td>
</tr>
<tr>
    <td class="item"><li>PostgreSQL Support</li></td>
    <td align="left"><? echo function_exists( 'pg_connect' ) ? '<b class="ok">'.$okImg.'</b><span class="item"></span>' : '<span class="error">'.$failedImg.' Not available</span>';?></td>
</tr>
<tr>
    <td class="item"><li>SQLite Support</li></td>
    <td align="left"><? echo function_exists( 'sqlite_open' ) ? '<b class="ok">'.$okImg.'</b><span class="item"> ('.sqlite_libversion().')</span>' : '<span class="error">'.$failedImg.' Not available</span>';?></td>
</tr>

<tr>
    <td class="title" colspan="2"><br />Check for Directory and File Permissions</td>
</tr>
<tr>
    <td class="item">Is folder "files" writable?</td>
    <td align="left"><?= CheckWritable("../files", "Can't upload files/images!"); ?></td>
</tr>
<tr>
    <td class="item">Is folder "log" writable?</td>
    <td align="left"><?= CheckWritable("../log", "Can't use webstat module!"); ?></td>
</tr>
<tr>
    <td class="item">Is folder "css" writable?</td>
    <td align="left"><?= CheckWritable("../css", "Can't edit CSS files located in ./css floder!"); ?></td>
</tr>
<tr>
    <td class="item">Is folder "images" writable?</td>
    <td align="left"><?= CheckWritable("../images", "Can't edit images located in ./images floder!"); ?></td>
</tr>
<tr>
    <td class="item">Is folder "xslt" writable?</td>
    <td align="left"><?= CheckWritable("../xslt", "Can't edit XSLT files located in ./xslt floder!"); ?></td>
</tr>
<tr>
    <td class="item">Is folder "tmp" writable?</td>
    <td align="left"><?= CheckWritable("../tmp", "Can't use JPCache and Gallery  modules!"); ?></td>
</tr>
<tr>
    <td class="item">Is "conf.d" writable?</td>
    <td align="left"><?= CheckWritable("../config/conf.d", "Can't write to config/conf.d folder!"); ?></td>
</tr>
<tr>
    <td class="title" colspan="2"><br/>More system information</td>
</tr>
<tr>
    <td class="item">Operating System?</td>
    <td align="left"><? echo '<b class="ok">'.$okImg.'</b><span class="item"> ('.php_uname().')</span>'; ?></td>
</tr>
<tr>
    <td class="item">Web Server?</td>
    <td align="left"><? echo '<b class="ok">'.$okImg.'</b><span class="item"> ('.$_SERVER['SERVER_SOFTWARE'].')</span>'; ?></td>
</tr>
</table>
<br />

<?

if (phpversion() < '5') $error = true;
if (!php_sapi_name() == "cgi") $error = true;
if (!DetectPEARDB());
if (!extension_loaded('xsl')) $error = true;
//if (!function_exists( 'mssql_connect' ) && !function_exists( 'mysql_connect' ) && !function_exists( 'pg_connect' ) && !function_exists( 'sqlite_open' )) $error = true;

function CheckWritable($file, $msg) {
    global $okImg, $failedImg;
    return (file_exists($file) && is_writable($file))  ? '<b class="ok">'.$okImg.'</b>'.$okMessage : '<b class="error">'.$failedImg.'</b><span class="error">' . $msg . '</span>';
}




function DetectPEARDB() {
    $incl_ = ini_get("include_path");
    
    $incl = split("[:;]", $incl_);
    for ($i = 0; $i < count($incl); $i++) {
        $FILE = $incl[$i] . "/DB.php";
        if (file_exists($FILE)) {
            return true;
        }
    }
    return false;
}

?>