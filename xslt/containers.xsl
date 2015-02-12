<?xml version='1.0'?>
<xsl:stylesheet exclude-result-prefixes="rdf rss xsl"
    version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rss="http://purl.org/rss/1.0/">
<xsl:template name="WriteModuleContainer">
    <xsl:for-each select="module">
        <div class="container_module"><xsl:value-of select="data" disable-output-escaping="yes" /></div>
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteTextNews">
    <xsl:for-each select="news">
        <div class="date"><xsl:value-of select="date" disable-output-escaping="yes" /></div>
        <a href="/en/{tid}.html" ><xsl:value-of select="title" disable-output-escaping="yes" /></a> :: <xsl:value-of select="shorttext" disable-output-escaping="yes" />
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteRDFNews">
    <xsl:param name="count" />
    <xsl:for-each select="rdf:RDF">
        <xsl:for-each select="rss:item">
            <xsl:if test="($count + 1) > position()">
                <li><a href="{rss:link}" target="_blank"><xsl:value-of disable-output-escaping="yes" select="rss:title"/></a></li>
            </xsl:if>
        </xsl:for-each>
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteRSSNews">
    <xsl:param name="count" />
    <xsl:for-each select="//*/rss/channel">
        <xsl:for-each select="item">
            <xsl:if test="($count + 1) > position()">
                <li><a href="{link}" target="_blank"><xsl:value-of disable-output-escaping="yes" select="title"/></a></li>
            </xsl:if>
        </xsl:for-each>
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteAtomNews">
    <xsl:param name="count" />
    <xsl:for-each select="atom:feed">
        <xsl:for-each select="atom:entry">
            <xsl:if test="($count + 1) > position()">
                <xsl:choose>
					<xsl:when test="atom:subtitle">
                        <li><a href="{atom:link/@href}" target="_blank"><strong><xsl:value-of disable-output-escaping="yes" select="atom:subtitle"/></strong></a><br /><xsl:value-of disable-output-escaping="yes" select="atom:category/@label"/></li>
                    </xsl:when>
					<xsl:otherwise>
						<li><a href="{atom:link/@href}" target="_blank"><strong><xsl:value-of disable-output-escaping="yes" select="atom:title"/></strong></a><br /><xsl:value-of disable-output-escaping="yes" select="atom:category/@label"/></li>
					</xsl:otherwise>
				</xsl:choose>
            </xsl:if>
        </xsl:for-each>
    </xsl:for-each>
</xsl:template>
</xsl:stylesheet>