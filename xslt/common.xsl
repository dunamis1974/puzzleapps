<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:template name="WriteTitlePath">
    <xsl:for-each select="//*[@tree = 'true']"> | <xsl:value-of select="title" disable-output-escaping="yes" /></xsl:for-each>
</xsl:template>

<xsl:template name="WritePath">
    <xsl:for-each select="//*[@tree = 'true']"><img src="/images/go.gif" class="pathArrow" /><a href="/{tid}.html"><xsl:value-of select="title" disable-output-escaping="yes" /></a></xsl:for-each>
</xsl:template>


</xsl:stylesheet>