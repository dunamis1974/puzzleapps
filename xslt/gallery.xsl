<?xml version="1.0"?>
<xsl:stylesheet exclude-result-prefixes="rdf rss xsl"
    version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rss="http://purl.org/rss/1.0/">
<xsl:output method="xml" indent="yes" omit-xml-declaration="yes" encoding="UTF-8" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"  doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" />
<xsl:param name="adminmode" select="//*/internal/adminmode" />
<xsl:param name="clang" select="//*/internal/lang" />
<xsl:param name="gettid" select="//*/internal/gettid" />
<xsl:param name="maintree" select="count(//*/text[@zone = 'main' and @tree = 'true'])" />

<!-- include common templates -->
<xsl:include href="./xslt/common.xsl"/>
<xsl:include href="./xslt/modules.xsl"/>
<xsl:include href="./xslt/containers.xsl"/>

<xsl:template match="platform">
<html>
<head>
    <title>PuzzleApps CMS Demonstration <xsl:call-template name="WriteTitlePath" /></title>
    <xsl:value-of select="//*/internal/head" disable-output-escaping="yes" />
    <meta name="description" content="{category[@tree = 'true']/description}" />
    <meta name="keywords" content="{category[@tree = 'true']/keywords}" />
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
    <xsl:if test="$adminmode = 1">
        <link rel="stylesheet" type="text/css" href="/css/admin.css" />
        <script src="/admin/scripts/popup.js" type="text/javascript"></script>
        <script src="/admin/scripts/context.js" type="text/javascript"></script>
    </xsl:if>
    <script src="/js/date.js" type="text/javascript"></script>
    <script src="/js/jquery.min.js" type="text/javascript"></script>
    <script src="/js/gallery.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/css/gallery.css" />
    <link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<div id="wrap">
    <!--header -->
    <div id="header">
        <h1 id="logo-text"><a href="index.html">PuzzleApps CMS</a></h1>		
        <p id="slogan">Easy ... Fast ... Flexible ...</p>		

        <div id="header-links">
            <p><xsl:call-template name="WriteCAT" /></p><span class="null" pas="cat" />
        </div>		
    </div><span class="null" pas="cat2" />

    <!-- menu -->	
    <div  id="menu">
        <xsl:call-template name="WriteCAT2" />
    </div>
    
    <!-- content-wrap starts here -->
    <div id="content-wrap">
        <div id="sidebar">
            <h3>Quick Access</h3>
            <ul class="sidemenu">				
                <li><a href="/index.html">Home</a></li>
                <xsl:call-template name="WriteQuick" />					
            </ul>
            <xsl:call-template name="WriteContainers" pas="rightperm" />
            <xsl:call-template name="WriteModuleCat" />
            <br /><br />
        </div>
        <div id="main">
            <span class="null" pas="main" />
            <xsl:call-template name="WriteBody" />
            <span class="null" pas="gallery" />
            <xsl:call-template name="WriteGallery" />
            <xsl:call-template name="WriteComments" />
            <xsl:call-template name="WriteModule" />
        </div>
    </div>
    <div id="footer">
        <p>
            <xsl:value-of select="'&amp;copy; &lt;strong>PuzzleApps&lt;/strong>'" disable-output-escaping="yes" /> | 
            Design by: <a href="http://www.styleshout.com/">styleshout</a> | Valid <a href="http://validator.w3.org/check?uri=referer">XHTML</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a><br />
            <xsl:call-template name="WriteCAT" /> | <a href="/feed.rss">RSS Feed</a>
        </p>

    </div>
</div>

</body>
</html>
</xsl:template>
<xsl:template name="WriteGallery">
    <div class="sc_menu">
        <ul class="sc_menu">
            <xsl:for-each select="image2[@zone = 'gallery']">
                <li pasobject="image2"><a href="/gallery/{@id}"><img src="/images/img.php?img={file}&amp;size=90" /><span><xsl:value-of select="name" disable-output-escaping="yes" /></span></a></li>
            </xsl:for-each>
        </ul>
    </div>
    
    <xsl:for-each select="image2[@zone = 'gallery' and @tree = 'true']">
        <h1><xsl:value-of select="name" disable-output-escaping="yes" /></h1>
        <img src="/images/img.php?img={file}&amp;size=400" class="galleryOpenImage" />
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteQuick">
    <xsl:for-each select="text[@zone = 'main' and $maintree = '0']">
        <li><a href="#{@id}"><xsl:value-of select="title" disable-output-escaping="yes" /></a></li>
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteBody">
    <xsl:call-template name="WritePath" />
    <xsl:for-each select="text[@zone = 'main']">
        <a name="{@id}"></a>
        <xsl:if test="title">
            <h1 pasobject="text"><xsl:value-of select="title" disable-output-escaping="yes" /></h1>
        </xsl:if>
        <p id="article" pasobject="text">
            <xsl:if test="longtext">
                <xsl:value-of select="longtext" disable-output-escaping="yes" /><br />
            </xsl:if>
        </p>
    </xsl:for-each>
</xsl:template>
<!-- Comments templates -->
<xsl:template name="WriteComments">
    <xsl:for-each select="comment[@zone = 'comments']">
        <div class="post-footer commentUser" pasobject="comment">
            <div class="userDataPos">
                <xsl:if test="name">
                    <span class="commentName"><xsl:value-of select="name" disable-output-escaping="yes" /> </span>
                </xsl:if>
                <span class="date"><script type="text/javascript">document.write(time2date(<xsl:value-of select="@date" />));</script></span>
            </div>
            <xsl:if test="rating">
                <div class="ratingPos"><div class="rating_bar"><div style="width:{rating * 20}%"></div></div></div>
            </xsl:if>
        </div>
        <xsl:if test="$adminmode = 1">
            <p>e-mail: <xsl:value-of select="email" disable-output-escaping="yes" /></p>
        </xsl:if>
        <xsl:if test="comment">
            <div class="commentData">
                <xsl:call-template name="lf2br">
                    <xsl:with-param name="StringToTransform" select="comment" />
                </xsl:call-template>
            </div>
        </xsl:if>
    </xsl:for-each>
</xsl:template>

<xsl:template name="lf2br">
    <xsl:param name="StringToTransform" />
    <xsl:choose>
        <xsl:when test="contains($StringToTransform,'&#10;')">
            <xsl:value-of select="substring-before($StringToTransform,'&#10;')" />
            <br />
            <xsl:call-template name="lf2br">
                <xsl:with-param name="StringToTransform" select="substring-after($StringToTransform,'&#10;')" />
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$StringToTransform" disable-output-escaping="yes" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>
<xsl:template name="WriteCAT">
    <xsl:for-each select="category[@zone = 'cat']">
        <a href="/{tid}.html" class="menu" pasobject="category"><xsl:value-of select="title" /></a>
        <xsl:if test="last() > position()"> | </xsl:if>
    </xsl:for-each>
</xsl:template>
<xsl:template name="WriteCAT2">
    <ul>
        <xsl:for-each select="category[@zone = 'cat2']">
                <xsl:choose>
                    <xsl:when test="last() > position()">
                        <xsl:choose>
                            <xsl:when test="@tree = 'true'"><li id="current" pasobject="category"><a href="/{tid}.html" class="hover"><xsl:value-of select="title" disable-output-escaping="yes" /></a></li></xsl:when>
                            <xsl:otherwise><li pasobject="category"><a href="/{tid}.html"><xsl:value-of select="title" disable-output-escaping="yes" /></a></li></xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="@tree = 'true'"><li id="current" class="last" pasobject="category"><a href="/{tid}.html" class="hover"><xsl:value-of select="title" disable-output-escaping="yes" /></a></li></xsl:when>
                            <xsl:otherwise><li class="last" pasobject="category"><a href="/{tid}.html"><xsl:value-of select="title" disable-output-escaping="yes" /></a></li></xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
        </xsl:for-each>
    </ul>
</xsl:template>
<xsl:template name="WriteRight">
    <table width="100%">
    <xsl:for-each select="text[@zone = 'right']">
        <tr><td pasobject="text">
        <xsl:if test="title">
            <h2><xsl:value-of select="title" disable-output-escaping="yes" /></h2>
        </xsl:if>
        <xsl:if test="shorttext">
            <div class="shorttext"><xsl:value-of select="shorttext" disable-output-escaping="yes" /></div>
        </xsl:if>
        <xsl:if test="image"><div align="{align}"><img src="/files/{image}" alt="{image}" /></div></xsl:if>
        <xsl:if test="longtext">
            <div class="longtext"><xsl:value-of select="longtext" disable-output-escaping="yes" /></div>
        </xsl:if>
        <p />
        </td></tr>
    </xsl:for-each>
    </table>
</xsl:template>
<xsl:template name="WriteContainers">
    <xsl:for-each select="container[@zone = 'rightperm']">
        <h3><xsl:value-of select="name" disable-output-escaping="yes" /></h3>
        <div class="left-box" pasobject="container">
            <xsl:if test="type = 'category'">
                <xsl:call-template name="WriteTextNews" />
            </xsl:if>
            <xsl:if test="type = 'rdf'">
                <ul class="sidemenu">
                <xsl:call-template name="WriteRDFNews">
                    <xsl:with-param name="count" select="count" />
                </xsl:call-template>
                </ul>
            </xsl:if>
            <xsl:if test="type = 'rss'">
                <ul class="sidemenu">
                <xsl:call-template name="WriteRSSNews">
                    <xsl:with-param name="count" select="count" />
                </xsl:call-template>
                </ul>
            </xsl:if>
            <xsl:if test="type = 'atom'">
                <ul class="sidemenu">
                <xsl:call-template name="WriteAtomNews">
                    <xsl:with-param name="count" select="count" />
                </xsl:call-template>
                </ul>
            </xsl:if>
            <xsl:if test="type = 'module'">
                <xsl:call-template name="WriteModuleContainer" />
            </xsl:if>
            <xsl:if test="type = 'exact'">
                <xsl:value-of select="exact" disable-output-escaping="yes" />
            </xsl:if>
            <xsl:if test="type = 'php'">
                <xsl:value-of select="result" disable-output-escaping="yes" />
            </xsl:if>
        </div>
    </xsl:for-each>
</xsl:template>
</xsl:stylesheet>