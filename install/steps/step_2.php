<table align="center" width="400" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="title" colspan="2">Write configuration</td>
</tr>
<tr>
    <td class="item" colspan="2">
    Please select Database servr you want to use and enter corect data i.e. server host, database name, user name and password.<br />
    <i><b>NOTE:</b><br />
    <li>server host, user name and password are used only if you select MySql, PostreSQL or MSSQL.<br />
    <li>For SQLite use 'Server host' to enter the name of the database file.<br />
    <li>This script will not create user and/or database. They should exist!
    </i>
    </td>
</tr>
<tr>
    <td class="item" valign="top" align="right">Select database</td>
    <td align="left"><?
        if (function_exists( 'mysql_connect' )) echo "<input type=\"radio\" name=\"driver\" value=\"mysql\" checked /> MySql<br />";
        if (function_exists( 'pg_connect' )) echo "<input type=\"radio\" name=\"driver\" value=\"pgsql\" /> PostgreSql<br />";
        if (function_exists( 'sqlite_open' )) echo "<input type=\"radio\" name=\"driver\" value=\"sqlite\" /> SQLite<br />";
        if (function_exists( 'mssql_connect' )) echo "<input type=\"radio\" name=\"driver\" value=\"mssql\" /> MSSQL<br />";
        ?>
    </td>
</tr>
<tr>
    <td align="right" class="item">Server host</td>
    <td align="left"><input type="text" name="host" /></td>
</tr>
<tr>
    <td align="right" class="item">Database</td>
    <td align="left"><input type="text" name="database" /></td>
</tr>
<tr>
    <td align="right" class="item">DB Username</td>
    <td align="left"><input type="text" name="dbuser" /></td>
</tr>
<tr>
    <td align="right" class="item">DB Password</td>
    <td align="left"><input type="text" name="dbpass" /></td>
</tr>
</table>
