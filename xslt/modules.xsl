<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="WriteModule">
    <table width="100%" align="center">
        <tr><td></td></tr>
    <xsl:for-each select="module">
        <tr><td>
            <xsl:value-of select="data" disable-output-escaping="yes" />
            <p />
        </td></tr>
    </xsl:for-each>
    </table>
</xsl:template>
<xsl:template name="WriteModuleCat">
    <xsl:for-each select="module">
        <tr>
            <td>
                <xsl:value-of select="text" disable-output-escaping="yes" />
                <xsl:value-of select="category" disable-output-escaping="yes" />
                <p />
            </td>
        </tr>
    </xsl:for-each>
</xsl:template>

</xsl:stylesheet>